let handler;
let datasetCreateFromFileMock;
let datasetWriteToFileMock;
let datasetApplyForEachRecordMock;
let output = '';

console.log = text => {
  output += `${text}\n`;
};

beforeEach(() => {
  jest.resetModules();

  output = '';

  const Dataset = require('./dataset');

  datasetCreateFromFileMock = jest
    .fn()
    .mockImplementationOnce(
      file =>
        new Dataset(
          ['user_id', 'email'],
          [{ user_id: 'a', email: 'a@example.com' }, { user_id: 'b', email: 'b@example.com' }],
          file,
        ),
    )
    .mockImplementationOnce(
      file =>
        new Dataset(
          ['user_id', 'first_name'],
          [{ user_id: 'a', first_name: 'A' }, { user_id: 'b', first_name: 'B' }],
          file,
        ),
    );

  datasetWriteToFileMock = jest.fn();
  datasetApplyForEachRecordMock = jest.fn();

  Dataset.createFromFile = datasetCreateFromFileMock;
  Dataset.prototype.writeToFile = datasetWriteToFileMock;
  Dataset.prototype.applyForEachRecord = datasetApplyForEachRecordMock;

  handler = require('./handler');
});

it('should throw exception if there are less than 2 files', async () => {
  expect.assertions(1);

  await expect(handler([], {})).rejects.toThrow();
});

it('should print to console if no "output" option passed', async () => {
  await handler(['file_a.csv', 'file_b.csv'], { column: 'user_id' });

  expect(datasetApplyForEachRecordMock).toHaveBeenCalled();
  expect(datasetCreateFromFileMock).toHaveBeenCalledTimes(2);
  expect(output).toEqual('user_id,email,first_name\na,a@example.com,A\nb,b@example.com,B\n\n');
});

it('should write to file if "output" option passed', async () => {
  await handler(['file_a.csv', 'file_b.csv'], { column: 'user_id', output: 'results.csv' });

  expect(datasetApplyForEachRecordMock).toHaveBeenCalled();
  expect(datasetWriteToFileMock).toHaveBeenCalledWith('results.csv');
  expect(output).toEqual('Done!\n');
});
