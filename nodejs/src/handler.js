const Dataset = require('./dataset');
const userUpdater = require('./user-updater');

module.exports = async (files, { output, column }) => {
  if (files.length < 2) {
    throw new Error('Please, provide at least 2 files');
  }

  const [firstDataset, ...otherDatasets] = await Promise.all(
    files.map(file => Dataset.createFromFile(file)),
  );
  const resultDataset = otherDatasets.reduce((prev, curr) => {
    return prev.join(curr, column);
  }, firstDataset);

  await resultDataset.applyForEachRecord(userUpdater);

  if (output) {
    await resultDataset.writeToFile(output);
    console.log('Done!');
  } else {
    console.log(await resultDataset.toCSVString());
  }
};
