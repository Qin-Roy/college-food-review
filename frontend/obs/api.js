import config from './Configuration.js'
import policy from './getPolicy.js'
import sign from './GetSignature.js'
// const policy = require('./getPolicy.js')
// const signature = require('./GetSignature.js')

export default {
	myUploadFile(key, FilePath, FileType, bucket) {
		return new Promise(function(resolve) {
			uni.showLoading({
				title: "上传中，请稍后",
				mask:true
			})
			console.log('key', key)
			console.log('FilePath', FilePath)
			console.log('FileType', FileType)
			//设定policy内容
			let nowDate = new Date()
			let future = new Date(nowDate.getFullYear() + 1, nowDate.getMonth(), nowDate.getDate())
			let EndPoint = "https://" + bucket + ".obs.cn-east-3.myhuaweicloud.com"
			let OBSPolicy = {
				"expiration": future, // Policy过期时间 uniapp ios Android 端 必须为此格式
				"conditions": [{
						"bucket": bucket
					}, //Bucket name
					{
						'key': key
					} // 格式 'xx/a.jpg' 路径/名称.格式 (路径选填)
				]
			}
			let policyEncoded = policy.getPolicyEncode(OBSPolicy); //计算policy编码值
			let signature = sign.getSignature(policyEncoded, config.SecretKey);
			let formData = {
				'key': key,
				'AccessKeyId': config.AccessKeyId,
				'Policy': policyEncoded,
				'Signature': signature,
				'content-type': FileType //文件类型
			}
			uni.uploadFile({
				url: EndPoint,
				filePath: FilePath, //临时文件地址
				name: 'file',
				formData: formData,
				success: (res) => {
					uni.hideLoading()
					uni.showToast({
						title:"上传成功"
					})
					console.log('OBS直传成功', res)
					resolve(EndPoint + '/' + key)
				},
				fail: (error) => {
					uni.hideLoading()
					uni.showToast({
						title:"上传失败"
					})
					console.log(error)
				}
			})
		})
	}
}
