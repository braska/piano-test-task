const { searchUser } = require('./services/piano-api');

module.exports = async localUser => {
  const remoteUser = await searchUser({ email: localUser.email });

  if (!remoteUser) {
    return localUser;
  }

  return {
    ...localUser,
    user_id: remoteUser.uid,
  };
};
