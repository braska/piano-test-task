const Dataset = require('./dataset');

const dataset1 = {
  file: 'file_a.csv',
  header: ['user_id', 'email'],
  records: [{ user_id: 'asdasdas', email: 'a@b.c' }],
};

const dataset2 = {
  file: 'file_a.csv',
  header: ['user_id', 'first_name'],
  records: [{ user_id: 'asdasdas', first_name: 'Test' }],
};

let dataset1Instance;

beforeEach(() => {
  dataset1Instance = new Dataset(dataset1.header, dataset1.records, dataset1.file);
});

describe('columnExists', () => {
  it('should return true if column exists', () => {
    // Arrange
    const expectedResult = true;

    // Act
    const actualResult = dataset1Instance.columnExists('user_id');

    // Assert
    expect(actualResult).toBe(expectedResult);
  });

  it("should return false if column doesn't exists", () => {
    // Arrange
    const expectedResult = false;

    // Act
    const actualResult = dataset1Instance.columnExists('some_column');

    // Assert
    expect(actualResult).toBe(expectedResult);
  });
});

describe('join', () => {
  let dataset2Instance;

  beforeEach(() => {
    dataset2Instance = new Dataset(dataset2.header, dataset2.records, dataset2.file);
  });

  it('should return new dataset if join column exists', () => {
    const expectedHeader = ['user_id', 'email', 'first_name'];
    const expectedRecords = [{ user_id: 'asdasdas', email: 'a@b.c', first_name: 'Test' }];

    const result = dataset1Instance.join(dataset2Instance, 'user_id');

    expect(result).toBeInstanceOf(Dataset);
    expect(result.header).toEqual(expectedHeader);
    expect(result.records).toEqual(expectedRecords);
  });

  it("should throw exception if join column doesn't exists", () => {
    expect(() => {
      dataset1Instance.join(dataset2Instance, 'some_column');
    }).toThrow();
  });
});

describe('applyForEachRecord', () => {
  it('should call function for each record', () => {
    const fn = jest.fn();

    dataset1Instance.applyForEachRecord(fn);

    expect(fn).toHaveBeenCalledTimes(dataset1.records.length);

    dataset1.records.forEach((record, index) => {
      expect(fn).toHaveBeenNthCalledWith(index + 1, record);
    });
  });

  it('should modify records', async () => {
    const expectedObject = {};

    const fn = jest.fn(() => {
      return expectedObject;
    });

    await dataset1Instance.applyForEachRecord(fn);

    dataset1Instance.records.forEach(record => {
      expect(record).toBe(expectedObject);
    });
  });
});
