const util = require('util');
const fs = require('fs');
const csv = require('csv');

const csvParse = util.promisify(csv.parse);
const csvStringify = util.promisify(csv.stringify);
const readFile = util.promisify(fs.readFile);
const writeFile = util.promisify(fs.writeFile);

class Dataset {
  constructor(header, records, file, map = {}) {
    this.header = header;
    this.records = records;
    this.file = file;
    this.map = map;
  }

  static async createFromFile(file) {
    const csvContent = await readFile(file);
    let header;
    const records = await csvParse(csvContent, {
      columns: columns => {
        header = columns;
        return columns;
      },
    });

    return new Dataset(header, records, file);
  }

  join(dataset, mergeColumnName) {
    const isColumnExistsInThis = this.columnExists(mergeColumnName);
    const isColumnExistsInAnother = dataset.columnExists(mergeColumnName);

    if (!isColumnExistsInThis || !isColumnExistsInAnother) {
      throw new Error(
        `No "${mergeColumnName}" column found in ${
          isColumnExistsInThis ? dataset.file : this.file
        }`,
      );
    }

    const header = [
      mergeColumnName,
      ...[...this.header, ...dataset.header].filter(h => h !== mergeColumnName),
    ];

    if (!this.map[mergeColumnName]) {
      this.map[mergeColumnName] = this.records.reduce(
        (prev, record, index) => ({
          ...prev,
          [record[mergeColumnName]]: index,
        }),
        {},
      );
    }

    const newMap = {
      ...this.map,
      [mergeColumnName]: {
        ...this.map[mergeColumnName],
      },
    };

    const records = [...this.records.map(record => ({ ...record }))];

    dataset.records.forEach(record => {
      const recordIndex = newMap[mergeColumnName][record[mergeColumnName]];
      if (typeof recordIndex !== 'undefined') {
        Object.assign(records[recordIndex], record);
      } else {
        const length = records.push({ ...record });
        newMap[mergeColumnName][record[mergeColumnName]] = length - 1;
      }
    });

    return new Dataset(header, records, undefined, newMap);
  }

  columnExists(columnName) {
    return this.header.includes(columnName);
  }

  async applyForEachRecord(fn) {
    this.records = await Promise.all(this.records.map(async record => await fn(record)));
  }

  async writeToFile(file) {
    return writeFile(file, await this.toCSVString());
  }

  toCSVString() {
    return csvStringify(this.records, {
      header: true,
      columns: this.header,
    });
  }
}

module.exports = Dataset;
