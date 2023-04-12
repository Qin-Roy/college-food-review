const Base64 = require('./Base64.js');
export default {
	getPolicyEncode(policy) {
		const encodedPolicy = Base64.encode(JSON.stringify(policy));
		return encodedPolicy;
	}
}

// module.exports = getPolicyEncode;
