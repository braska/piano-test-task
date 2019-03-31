let userUpdater;
let mockApiUser = null;

beforeEach(() => {
  jest.resetModules();
  mockApiUser = null;

  jest.mock('./services/piano-api', () => ({
    searchUser() {
      return mockApiUser;
    },
  }));

  userUpdater = require('./user-updater');
});

test("should return unmodified user object if user doesn't presented on Piano", async () => {
  const expectedUser = { user_id: 'a', email: '2' };
  mockApiUser = null;

  const actualUser = await userUpdater(expectedUser);

  expect(actualUser).toEqual(expectedUser);
});

test('should return updated user object if user presented on Piano', async () => {
  const expectedUser = { user_id: 'a', email: '2' };
  mockApiUser = { uid: 'b' };

  const actualUser = await userUpdater(expectedUser);

  expect(actualUser.user_id).toEqual(mockApiUser.uid);
});
