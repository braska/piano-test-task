const axios = require('axios');
const qs = require('qs');
const config = require('../config');

module.exports = {
  async searchUser(params) {
    const response = await axios.post(
      '/publisher/user/search',
      qs.stringify({
        ...params,
        aid: config.aid,
        api_token: config.apiToken,
      }),
      {
        baseURL: config.baseURL,
      },
    );

    if (response.status !== 200 || !response.data || response.data.code !== 0) {
      throw new Error('Error while API request');
    }

    if (!response.data.total || response.data.total < 1) {
      return null;
    }

    return response.data.users[0];
  },
};
