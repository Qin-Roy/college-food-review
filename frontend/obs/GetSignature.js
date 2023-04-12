require('./hmac.js');
require('./sha1.js');
const Crypto = require('./crypto.js');
const Base64 = require('./Base64.js');

export default {
	//利用SK计算Signature信息
	getSignature(policyEncoded, SecretKey) {
		const bytes = Crypto.HMAC(Crypto.SHA1, policyEncoded, SecretKey, {
			asBytes: true
		});

		const signature = Crypto.util.bytesToBase64(bytes);
		return signature;
	}
}


// module.exports = getSignature;
