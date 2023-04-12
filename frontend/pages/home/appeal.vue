<!--申诉页面-->
<template>
	<view>
		<view style="color: red;text-align: center;font-size: 35rpx;padding-top: 20rpx;">您的账号已被封禁,请如实填写申诉信息!</view>
		<uni-card title="我要申诉" :thumbnail="informShow.pictureUrl" :extra="informShow.time" shadow="true">
			<uni-forms ref="form" :modelValue="formData" :rules="rules">

				<uni-section title="封禁原因" type="line">
					<text class="fontStyle">{{reportList[0].reason}}</text>
				</uni-section>

				<uni-section title="申诉理由" type="line">
					<uni-forms-item required name="reason">
						<uni-easyinput v-model="formData.reason" placeholder="请输入申诉理由"></uni-easyinput>
					</uni-forms-item>
				</uni-section>

				<uni-section title="详细信息" type="line">
					<uni-forms-item required name="detail">
						<uni-easyinput type="textarea" v-model="formData.detail" placeholder="请输入申诉详细信息">
						</uni-easyinput>
					</uni-forms-item>
				</uni-section>

				<uni-section title="佐证资料" subTitle="仅支持图片形式文件" type="line">
					<uni-forms-item name="pictureList">
						<uni-forms-item name="pictureList">
							<uni-file-picker auto-upload="false" @select="selectImage" @delete="deleteImage"
								v-model="formData.images" file-mediatype="image" limit="3" title="最多选择3张图片">
							</uni-file-picker>
						</uni-forms-item>
					</uni-forms-item>
				</uni-section>

			</uni-forms>

			<button class="gonButtonInform" @click="submitForm('form')">申诉</button>
			<button class="gonButton" @click="concelSubmit()">取消</button>
		</uni-card>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				informShow: {
					time: "00",
					pictureUrl: "../../../../static/icons/news-active.png",
				},
				reportList: [{
					reason: "00",
					detail: "000",
					pictureList: "000",
					time: "00"
				}],
				fileNum: 0,
				formData: {
					userId: "",
					reason: "",
					detail: "",
					images: [],
					tmp_images: []
				},
				result: {}, //返回提交情况
				// 校验规则
				rules: {
					reason: {
						rules: [{
							required: true,
							errorMessage: '理由不能为空'
						}]
					},
					detail: {
						rules: [{
							required: true,
							errorMessage: '详细信息不能为空'
						}]
					}
				},
			}
		},
		onLoad(option) {
			this.formData.userId = option.id; //获取申诉人ID
			this.informShow.time = this.dateTimeStr('y-m-d h:i') //获取当前时间
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getReport',
				method: "GET",
				data: {
					"id": this.formData.userId,
				},
				success: (res) => {
					console.log('获取成功' + res);
					this.reportList = res.data.data.reportList
				},
				fail: () => {
					console.log('获取失败');
				},
			})

		},
		methods: {
			selectFile(e) {
				uni.showToast({
					title: "选择成功",
					icon: 'none'
				})
				this.formData.filesList[this.fileNum] = e.tempFilePaths;
				this.fileNum = this.fileNum + 1;
			},
			dateTimeStr(str) {
				console.log(Date()); //show
				var date = new Date(),
					year = date.getFullYear(), //年
					month = date.getMonth() + 1, //月
					day = date.getDate(), //日
					hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(), //时
					minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(), //分
					second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds(); //秒
				month >= 1 && month <= 9 ? (month = "0" + month) : "";
				day >= 0 && day <= 9 ? (day = "0" + day) : "";
				//hour >= 0 && hour <= 9 ? (hour = "0" + hour) : "";
				//minute >= 0 && minute <= 9 ? (minute = "0" + minute) : "";
				//second >= 0 && second <= 9 ? (second = "0" + second) : "";
				if (str.indexOf('y') != -1) {
					str = str.replace('y', year)
				}
				if (str.indexOf('m') != -1) {
					str = str.replace('m', month)
				}
				if (str.indexOf('d') != -1) {
					str = str.replace('d', day)
				}
				if (str.indexOf('h') != -1) {
					str = str.replace('h', hour)
				}
				if (str.indexOf('i') != -1) {
					str = str.replace('i', minute)
				}
				if (str.indexOf('s') != -1) {
					str = str.replace('s', second)
				}
				return str;
			},
			submitForm(ref) {
				this.$refs[ref].validate().then(res => {
					uni.request({
						url: 'http://124.71.170.100/index.php/index/appeal',
						method: "POST",
						data: {
							"id": this.formData.userId,
							"reason": this.formData.reason,
							"detail": this.formData.detail,
							"pictureList": this.formData.tmp_images,
							"time": new Date().getTime()
						},
						success: (res) => {
							console.log('提交成功', res);
							uni.showToast({
								title: '提交成功',
								icon: 'none',
								mask: 'true'
							})
							uni.redirectTo({
								url: '../login/login'
							});
						},
						fail: () => {
							console.log('提交失败');
						},
					})
					console.log("姓名：" + this.informShow.userName);
					console.log("举报原因：" + this.formData.reason);
					console.log("具体理由：" + this.formData.detail);
					console.log('照片列表：' + this.formData.tmp_images)
					console.log("表单提交");
				});
			},
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.formData.images = []
				for (let i = 0; i < _this.formData.tmp_images.length; i++) {
					let temp = {
						"url": _this.formData.tmp_images[i]
					}
					_this.formData.images.push(temp);
				}
				for (let k = 0; k < e.tempFilePaths.length; k++) {
					_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[k].name, e.tempFilePaths[k], e.tempFiles[k]
							.fileType, "se-report")
						.then((res) => {
							console.log('success', res)
							// _this.formData.images.push(res)
							_this.formData.tmp_images.push(res)
							_this.formData.images = []
							for (let i = 0; i < _this.formData.tmp_images.length; i++) {
								let temp = {
									"url": _this.formData.tmp_images[i]
								}
								_this.formData.images.push(temp);
							}
						})
				}
			},
			deleteImage(e) {
				console.log(e)
				console.log(this.formData.images)
				for (let i = 0; i < this.formData.tmp_images.length; i++) {
					if (this.formData.tmp_images[i] == e.tempFilePath) {
						this.formData.tmp_images.splice(i, 1)
					}
				}
			},
			concelSubmit() {
				console.log("取消提交")
				uni.showToast({
					title: '取消成功',
					icon: 'none',
					mask: 'true'
				})
				uni.redirectTo({
					url: '../login/login'
				});
			}
		}
	}
</script>

<style>
	.selectBotton {
		size: ;
	}

	.idShow {
		color: firebrick;
		margin-left: 50rpx;
		text-align: center;
	}

	.gonButton {
		background-image: linear-gradient(to right, #fff795 0%, #ffa947 51%, #ffac82 100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block;
	}

	.gonButtonInform {
		background-image: linear-gradient(to right, #f4ffb9 0%, #ff7462 51%, #ffb969 100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block
	}

	.fontStyle {
		background-attachment: fixed;
		background-color: antiquewhite;
		font-weight: bold;
		font-stretch: expanded;
		color: #dd4816;
	}
</style>
