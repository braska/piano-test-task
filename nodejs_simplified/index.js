const fs = require("fs");
const https = require("https");

const readCSV = file =>
  fs
    .readFileSync(file, { encoding: "utf8" })
    .split("\n")
    .filter(row => row.length > 0)
    .map(row => row.split(","));

const csvA = readCSV("../_data/file_a.csv");
const csvB = readCSV("../_data/file_b.csv");

const [csvAHeader, ...csvARecords] = csvA;
const [csvBHeader, ...csvBRecords] = csvB;

const header = [
  "user_id",
  ...[...csvAHeader, ...csvBHeader].filter(h => h !== "user_id")
];

const csvAUIDIndex = csvAHeader.indexOf("user_id");
const csvBUIDIndex = csvBHeader.indexOf("user_id");

const recordIndexByUID = {};
const records = [];

const processCSVRecords = (csvRecords, uidIndex) => {
  csvRecords.forEach(record => {
    const recordIndex = recordIndexByUID[record[uidIndex]];
    const uid = record[uidIndex];

    const otherValues = [...record];
    otherValues.splice(uidIndex, 1);

    if (typeof recordIndex !== "undefined") {
      records[recordIndex].push(...otherValues);
    } else {
      const length = records.push([uid, ...otherValues]);
      recordIndexByUID[record[uidIndex]] = length - 1;
    }
  });
};

processCSVRecords(csvARecords, csvAUIDIndex);
processCSVRecords(csvBRecords, csvBUIDIndex);

const getUID = email => {
  const body = `email=${email}&aid=${process.env.PIANO_AID}&api_token=${
    process.env.PIANO_API_TOKEN
  }`;

  const options = {
    hostname: "sandbox.tinypass.com",
    port: 443,
    path: "/api/v3/publisher/user/search",
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "Content-Length": body.length
    }
  };
  return new Promise((resolve, reject) => {
    let data = "";

    const req = https.request(options, res => {
      if (res.statusCode !== 200) {
        reject(new Error(`status code: ${res.statusCode}`));
        return;
      }

      res.on("data", d => {
        data += d;
      });

      res.on("end", () => {
        const parsedJSON = JSON.parse(data);

        if (parsedJSON.code !== 0) {
          reject(parsedJSON);
          return;
        }

        if (!parsedJSON.total || parsedJSON.total < 1) {
          resolve(null);
          return;
        }

        resolve(parsedJSON.users[0].uid);
      });
    });

    req.on("error", error => {
      reject(error);
    });

    req.write(body);
    req.end();
  });
};

const emailIndex = header.indexOf("email");

const promises = records.map(record =>
  getUID(record[emailIndex])
    .then(uid => {
      if (uid) {
        record[0] = uid;
      }

      return record;
    })
    .catch(error => {
      console.error(error);
      process.exit(1);
    })
);

Promise.all(promises).then(records => {
  const result =
    [header.join(","), ...records.map(record => record.join(","))].join("\n") +
    "\n";

  fs.writeFileSync("result.csv", result);
});
