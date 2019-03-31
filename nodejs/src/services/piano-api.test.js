let mockResponse;
let mockPost;
let api;

beforeEach(() => {
  jest.resetModules();

  mockPost = jest.fn(() => mockResponse);

  const mockAxios = {
    post: mockPost,
  };
  jest.mock('axios', () => mockAxios);

  api = require('./piano-api');
});

describe('searchUser', () => {
  it('should throw exception if status is not equal to 200', async () => {
    expect.assertions(2);

    mockResponse = {
      status: 500,
    };

    await expect(api.searchUser()).rejects.toThrow();
    expect(mockPost).toHaveBeenCalled();
  });

  it('should throw exception if code is not equal to 0', async () => {
    expect.assertions(2);

    mockResponse = {
      status: 200,
      data: {
        code: 401,
      },
    };

    await expect(api.searchUser()).rejects.toThrow();
    expect(mockPost).toHaveBeenCalled();
  });

  it('should return null if user not found', async () => {
    expect.assertions(2);

    mockResponse = {
      status: 200,
      data: {
        total: 0,
        code: 0,
      },
    };

    const actualResult = await api.searchUser();

    expect(mockPost).toHaveBeenCalled();
    expect(actualResult).toEqual(null);
  });

  it('should return user object if user found', async () => {
    expect.assertions(2);

    const expectedUser = {
      uid: 'aaa',
    };

    mockResponse = {
      status: 200,
      data: {
        total: 1,
        code: 0,
        users: [expectedUser],
      },
    };

    const actualResult = await api.searchUser();

    expect(mockPost).toHaveBeenCalled();
    expect(actualResult).toEqual(expectedUser);
  });
});
