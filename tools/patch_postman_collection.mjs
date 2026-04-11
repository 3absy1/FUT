/**
 * Patch FUT.postman_collection.json
 */
import { readFileSync, writeFileSync } from "fs";
import { fileURLToPath } from "url";
import { dirname, join } from "path";

const __dirname = dirname(fileURLToPath(import.meta.url));
const PATH = join(__dirname, "..", "FUT.postman_collection.json");

const STADIUM_TOKEN_SCRIPT = [
  "let res = pm.response.json();\r",
  "\r",
  "if (res.result && res.result.token) {\r",
  "    pm.collectionVariables.set('stadiumOwnerAuthToken', res.result.token);\r",
  "    pm.environment.set('stadiumOwnerAuthToken', res.result.token);\r",
  "    pm.globals.set('stadiumOwnerAuthToken', res.result.token);\r",
  "    console.log('Saved stadiumOwnerAuthToken');\r",
  "} else {\r",
  "    console.log('Token not found', res);\r",
  "}\r",
];

const HEADERS = [
  { key: "Accept-Language", value: "en", type: "text" },
  { key: "X-Api-Key", value: "123456", type: "text" },
  { key: "Accept", value: "application/json", type: "text" },
];

const BEARER_STADIUM = {
  type: "bearer",
  bearer: [{ key: "token", value: "{{stadiumOwnerAuthToken}}", type: "string" }],
};

function urlObj(segments) {
  const raw = "{{Fut_local}}" + segments.join("/");
  return {
    raw,
    host: ["{{Fut_local}}" + segments[0]],
    path: segments.slice(1),
  };
}

function reqGet(segments, name, desc, query) {
  const u = urlObj(segments);
  if (query) u.query = query;
  return {
    name,
    request: {
      auth: structuredClone(BEARER_STADIUM),
      method: "GET",
      header: structuredClone(HEADERS),
      url: u,
      description: desc || "",
    },
    response: [],
  };
}

function reqPostJson(segments, name, body, desc) {
  return {
    name,
    request: {
      auth: structuredClone(BEARER_STADIUM),
      method: "POST",
      header: structuredClone(HEADERS),
      body: {
        mode: "raw",
        raw: JSON.stringify(body, null, 2),
        options: { raw: { language: "json" } },
      },
      url: urlObj(segments),
      description: desc || "",
    },
    response: [],
  };
}

function patchStadiumAuth(items) {
  for (const it of items) {
    if (it.name !== "auth") continue;
    for (const sub of it.item || []) {
      const req = sub.request || {};
      const url = req.url || {};
      if (sub.name === "verify-otp") {
        url.raw = "{{Fut_live}}auth/stadium/verify-otp";
        url.path = ["stadium", "verify-otp"];
        for (const resp of sub.response || []) {
          const orq = resp.originalRequest || {};
          const ou = orq.url || {};
          ou.raw = "{{Fut_local}}auth/stadium/verify-otp";
          ou.path = ["stadium", "verify-otp"];
        }
      } else if (sub.name === "login") {
        url.raw = "{{Fut_live}}auth/stadium/login";
        url.path = ["stadium", "login"];
        for (const resp of sub.response || []) {
          const ou = (resp.originalRequest || {}).url || {};
          ou.raw = "{{Fut_local}}auth/stadium/login";
          ou.path = ["stadium", "login"];
        }
        for (const ev of sub.event || []) {
          if (ev.listen === "test") ev.script.exec = STADIUM_TOKEN_SCRIPT;
        }
      } else if (sub.name === "logout") {
        req.auth = structuredClone(BEARER_STADIUM);
        for (const ev of sub.event || []) {
          if (ev.listen === "test") {
            ev.script.exec = [
              "pm.test('Logout OK', () => pm.response.code === 200);\r",
            ];
          }
        }
      }
    }
    break;
  }
}

function patchMatchOps(items) {
  for (const it of items) {
    if (it.name === "Match Requests accept By Stadium") {
      const req = it.request;
      req.auth = structuredClone(BEARER_STADIUM);
      req.header = structuredClone(HEADERS);
      req.body = {
        mode: "raw",
        raw: JSON.stringify({ pitch_id: 1 }, null, 2),
        options: { raw: { language: "json" } },
      };
      req.url = {
        raw: "{{Fut_local}}match-schedule-requests/{{matchScheduleRequestId}}/accept-by-stadium",
        host: ["{{Fut_local}}match-schedule-requests"],
        path: ["{{matchScheduleRequestId}}", "accept-by-stadium"],
      };
      it.response = [
        {
          name: "200 — accepted with pitch",
          originalRequest: structuredClone(req),
          status: "OK",
          code: 200,
          header: [{ key: "Content-Type", value: "application/json" }],
          body: JSON.stringify(
            {
              result: {
                request: {
                  id: 1,
                  stadium_id: 1,
                  match_id: 10,
                  status: "scheduled",
                  match: {
                    id: 10,
                    pitch_id: 1,
                    scheduled_datetime: "2026-04-12T18:00:00+00:00",
                    pitch: {
                      id: 1,
                      stadium_id: 1,
                      name: "Pitch A",
                      sort_order: 0,
                    },
                  },
                },
              },
              message: "Stadium accepted and match created.",
              statusCode: 200,
              statusName: "OK",
            },
            null,
            2
          ),
        },
        {
          name: "422 — pitch_id required",
          status: "Unprocessable Content",
          code: 422,
          header: [{ key: "Content-Type", value: "application/json" }],
          body: JSON.stringify(
            {
              message: "The pitch id field is required.",
              title: "The pitch id field is required.",
              code: 422,
              errorsList: ["The pitch id field is required."],
            },
            null,
            2
          ),
        },
      ];
      it.event = it.event || [];
      for (const ev of it.event) {
        if (ev.listen === "test") {
          ev.script.exec = [
            "let res = pm.response.json();\r",
            "if (res.result && res.result.request && res.result.request.match_id) {\r",
            "    pm.collectionVariables.set('matchId', String(res.result.request.match_id));\r",
            "}\r",
          ];
        }
      }
    }
    if (it.name === "Match Requests Record Result") {
      const req = it.request;
      req.auth = structuredClone(BEARER_STADIUM);
      req.header = structuredClone(HEADERS);
      req.body = {
        mode: "raw",
        raw: JSON.stringify(
          { winner: "club_a", score_club_a: 3, score_club_b: 1 },
          null,
          2
        ),
        options: { raw: { language: "json" } },
      };
      req.url = {
        raw: "{{Fut_local}}matches/{{matchId}}/record-result",
        host: ["{{Fut_local}}matches"],
        path: ["{{matchId}}", "record-result"],
      };
      it.response = [
        {
          name: "200 — result recorded",
          status: "OK",
          code: 200,
          header: [{ key: "Content-Type", value: "application/json" }],
          body: JSON.stringify(
            {
              result: {
                match: {
                  id: 10,
                  status: "completed",
                  score_club_a: 3,
                  score_club_b: 1,
                  pitch_id: 1,
                },
              },
              message: "Match result recorded and EXP updated.",
              statusCode: 200,
              statusName: "OK",
            },
            null,
            2
          ),
        },
      ];
    }
  }
}

function stadiumDashboardFolder() {
  return {
    name: "Stadium dashboard",
    description:
      "Uses `stadiumOwnerAuthToken`. Template vars: `matchScheduleRequestId`, `matchId`, `pitchId`.",
    item: [
      reqGet(["auth", "stadium", "profile"], "GET profile", "User + stadium.area + stadium.pitches"),
      reqGet(
        ["stadium", "match-schedule-requests"],
        "GET stadium match-schedule-requests",
        "?status=pending optional",
        [{ key: "status", value: "pending", disabled: true }]
      ),
      reqGet(["stadium", "matches"], "GET stadium matches (history)", "Paginated"),
      reqPostJson(
        ["stadium", "matches"],
        "POST stadium manual match",
        {
          club_a_id: 1,
          club_b_id: 2,
          pitch_id: 1,
          scheduled_datetime: "2026-12-01 20:00:00",
          status: "scheduled",
        },
        "Manual match (no schedule request)"
      ),
      reqGet(["stadium", "pitches"], "GET stadium pitches", ""),
      reqPostJson(
        ["stadium", "pitches"],
        "POST stadium pitch",
        { name: "Pitch A", sort_order: 0 },
        ""
      ),
      {
        name: "PUT stadium pitch",
        request: {
          auth: structuredClone(BEARER_STADIUM),
          method: "PUT",
          header: structuredClone(HEADERS),
          body: {
            mode: "raw",
            raw: JSON.stringify({ name: "Pitch A renamed", sort_order: 1 }, null, 2),
            options: { raw: { language: "json" } },
          },
          url: urlObj(["stadium", "pitches", "{{pitchId}}"]),
        },
        response: [],
      },
      {
        name: "DELETE stadium pitch",
        request: {
          auth: structuredClone(BEARER_STADIUM),
          method: "DELETE",
          header: structuredClone(HEADERS),
          url: urlObj(["stadium", "pitches", "{{pitchId}}"]),
        },
        response: [],
      },
    ],
  };
}

function divisionsFolder() {
  const bearerUser = {
    type: "bearer",
    bearer: [{ key: "token", value: "{{userAuthToken}}", type: "string" }],
  };
  return {
    name: "Divisions",
    description: "Player token only (`userAuthToken`).",
    item: [
      {
        name: "divisions index",
        request: {
          auth: structuredClone(bearerUser),
          method: "GET",
          header: structuredClone(HEADERS),
          url: urlObj(["divisions"]),
        },
        response: [],
      },
      {
        name: "divisions show",
        request: {
          auth: structuredClone(bearerUser),
          method: "GET",
          header: structuredClone(HEADERS),
          url: urlObj(["divisions", "1"]),
        },
        response: [],
      },
    ],
  };
}

const data = JSON.parse(readFileSync(PATH, "utf8"));

function patchPlayerStadiumWrites(data) {
  const player = data.item.find((x) => x.name === "Player");
  const stadiums = (player?.item || []).find((x) => x.name === "Stadiums");
  for (const it of stadiums?.item || []) {
    if (
      ["stadiums create", "stadiums update", "stadiums delete"].includes(it.name)
    ) {
      const b = it.request?.auth?.bearer?.[0];
      if (b) b.value = "{{stadiumOwnerAuthToken}}";
    }
  }
}

for (const top of data.item) {
  if (top.name === "Stadium Owner") {
    const items = top.item;
    patchStadiumAuth(items);
    patchMatchOps(items);
    if (!items.some((x) => x.name === "Stadium dashboard")) {
      const ai = items.findIndex((x) => x.name === "auth");
      items.splice(ai + 1, 0, stadiumDashboardFolder());
    }
  }
  if (top.name === "Player") {
    const items = top.item;
    if (!items.some((x) => x.name === "Divisions")) {
      const si = items.findIndex((x) => x.name === "Stadiums");
      items.splice(si, 0, divisionsFolder());
    }
  }
}

patchPlayerStadiumWrites(data);

function fixStadiumOwnerRegisterExamples(data) {
  const so = data.item.find((x) => x.name === "Stadium Owner");
  const auth = (so?.item || []).find((x) => x.name === "auth");
  const reg = (auth?.item || []).find((x) => x.name === "register");
  const responses = reg?.response || [];
  const ok = responses.find((r) => r.name === "register");
  if (ok) {
    ok.originalRequest = {
      method: "POST",
      header: structuredClone(HEADERS),
      body: {
        mode: "raw",
        raw: JSON.stringify(
          {
            name: "Stadium Owner One",
            nick_name: "stadium_owner_1",
            phone: "01098765432",
            email: "owner@example.com",
            birth_date: "1990-01-15",
            password: "Password@123",
            password_confirmation: "Password@123",
            stadium_id: 1,
            fcm_token: null,
          },
          null,
          2
        ),
        options: { raw: { language: "json" } },
      },
      url: {
        raw: "{{Fut_local}}auth/stadium/register",
        host: ["{{Fut_local}}auth"],
        path: ["stadium", "register"],
      },
    };
    ok.body = JSON.stringify(
      {
        result: {
          user: {
            id: 5,
            name: "Stadium Owner One",
            nick_name: "stadium_owner_1",
            phone: "01098765432",
            email: "owner@example.com",
            birth_date: "1990-01-15",
            age: 36,
            is_verified: false,
            is_stadium_owner: true,
            stadium_id: 1,
            division: null,
            stadium: {
              id: 1,
              name: "Main Arena",
              pitches: [{ id: 1, stadium_id: 1, name: "Pitch A", sort_order: 0 }],
            },
            friends_count: 0,
            rating: 0,
            wallet_balance: "0.00",
            exp: 0,
            fcm_token: null,
            is_notification: true,
            created_at: "2026-04-11T12:00:00+00:00",
          },
        },
        message: "Stadium owner registration successful. Please verify with OTP (use 1111 for now).",
        statusCode: 201,
        statusName: "OK",
      },
      null,
      2
    );
  }
  const val = responses.find((r) => r.name === "validation errors");
  if (val) {
    val.body = JSON.stringify(
      {
        message: "The stadium id field is required.",
        title: "The stadium id field is required.",
        code: 422,
        errorsList: ["The stadium id field is required."],
      },
      null,
      2
    );
  }
}

fixStadiumOwnerRegisterExamples(data);

function fixPlayerRegisterValidationExample(data) {
  const player = data.item.find((x) => x.name === "Player");
  const auth = player?.item?.find((x) => x.name === "auth");
  const reg = auth?.item?.find((x) => x.name === "register");
  const val = reg?.response?.find((r) => r.name === "validation errors");
  if (val && String(val.body).includes("VALIDATION")) {
    val.body = JSON.stringify(
      {
        message: "The nick name field is required.",
        title: "The nick name field is required.",
        code: 422,
        errorsList: [
          "The nick name field is required.",
          "The phone has already been taken.",
        ],
      },
      null,
      2
    );
  }
}

fixPlayerRegisterValidationExample(data);

writeFileSync(PATH, JSON.stringify(data, null, 2) + "\n", "utf8");
console.log("Patched", PATH);
