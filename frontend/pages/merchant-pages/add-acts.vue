<!--商家申请活动页面-->
<template>
	<view class="add-acts">
		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules" :modelValue="customFormData">
				<uni-section title="活动信息" type="line">
					<uni-forms-item label="活动名称" required name="name">
						<uni-easyinput maxlength="30" v-model="customFormData.name" placeholder="请输入活动名称" />
					</uni-forms-item>
					<uni-forms-item label="活动介绍" required name="introduction">
						<uni-easyinput maxlength="100" type="textarea" v-model="customFormData.intro"
							placeholder="请输活动介绍" />
					</uni-forms-item>
					<uni-forms-item label="活动标语" required name="slogan">
						<uni-easyinput maxlength="50" v-model="customFormData.slogan" placeholder="请输入活动标语" />
					</uni-forms-item>
					<uni-forms-item label="开始时间" required name="startTime">
						<uni-datetime-picker type="datetime" return-type="timestamp"
							v-model="customFormData.startTime" />
					</uni-forms-item>
					<uni-forms-item label="截至时间" required name="endTime">
						<uni-datetime-picker type="datetime" return-type="timestamp" v-model="customFormData.endTime" />
					</uni-forms-item>
				</uni-section>

				<uni-section title="活动封面照片" type="line">
					<uni-file-picker auto-upload="false" @select="selectImage" v-model="customFormData.picture"
						file-mediatype="image" returnType="object" limit="1" title="请选择1张图片"></uni-file-picker>
					<text v-if="customFormData.picture.length == 0" style="color: red;">请选择一张照片</text>
				</uni-section>

			</uni-forms>
			<view class="myButtonArea">
				<button class="myButton" @click="submit('customForm','../home/my')">提交</button>
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				userInfo: {},
				customFormData: {
					name: "",
					avtivityId: "",
					intro: "",
					startTime: "",
					endTime: "",
					picture: [],
					slogan: "",
					last_startTime: "",
					last_endTime: ""
				},
				nowdate: '',
				customRules: {
					name: {
						rules: [{
							required: true,
							errorMessage: '活动名称不能为空'
						}]
					},
					introduction: {
						rules: [{
							required: true,
							errorMessage: "活动介绍不能为空"
						}]
					},
					slogan: {
						rules: [{
							required: true,
							errorMessage: '活动标语不能为空'
						}]
					},
					startTime: {
						rules: [{
							required: true,
							errorMessage: "活动开始时间不能为空"
						}]
					},
					endTime: {
						rules: [{
							required: true,
							errorMessage: '活动结束时间不能为空'
						}]
					}
				}
			}
		},
		onLoad: function(option) {
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data
					that.status = option.status;
					if (option.status == 1) {
						uni.request({
							url: 'http://124.71.170.100/index.php/index/getActivityInfo',
							method: "GET",
							data: {
								activityId: option.id
							},
							success: (res) => {
								console.log('获取用户信息', res)
								console.log("startTime:", this.changeTime(res.data.startTime))
								that.customFormData.name = res.data.activityName;
								that.customFormData.activityId = option.id;
								that.customFormData.intro = res.data.intro;
								that.last_startTime = res.data.startTime;
								that.last_endTime = res.data.endTime;
								that.customFormData.startTime = this.changeTime(res.data
									.startTime);
								that.customFormData.endTime = this.changeTime(res.data
									.endTime);
								that.customFormData.slogan = res.data.slogan;
								if (res.data.picture != "") {
									let temp = {
										"url": res.data.picture
									}
									this.customFormData.picture.push(temp);
								}
							}
						})
					}
				}
			})
		},
		methods: {
			submit(ref, url) {
				this.$refs[ref].validate().then(res => {
					if (this.customFormData.picture.length != 0) {
						console.log('success', res);
						if (this.status == 1) {
							uni.request({
								url: 'http://124.71.170.100/index.php/index/editActivity',
								header: {
									'content-type': 'application/x-www-form-urlencoded'
								},
								method: "POST",
								data: {
									activityName: this.customFormData.name,
									intro: this.customFormData.intro,
									picture: this.customFormData.picture == null || this.customFormData
										.picture
										.length == 0 ? "" : this.customFormData
										.picture[0].url,
									merchantId: this.userInfo.id,
									activityId: this.customFormData.activityId,
									slogan: this.customFormData.slogan,
									startTime: this.changeTime(this.last_startTime) == this.customFormData
										.startTime ? this.last_startTime : this.customFormData.startTime,
									endTime: this.changeTime(this.last_endTime) == this.customFormData
										.endTime ? this.last_endTime : this.customFormData.endTime,
								},
								success: (res2) => {
									console.log(res2);
									uni.navigateBack()
									uni.showToast({
										title: `提交成功`,
										mask:true
									})
								}
							})
						} else {
							uni.request({
								url: 'http://124.71.170.100/index.php/index/addActivity',
								header: {
									'content-type': 'application/x-www-form-urlencoded'
								},
								method: "POST",
								data: {
									activityName: this.customFormData.name,
									intro: this.customFormData.intro,
									picture: this.customFormData.picture == null || this.customFormData
										.picture
										.length == 0 ? "" : this.customFormData
										.picture[0].url,
									merchantId: this.userInfo.id,
									slogan: this.customFormData.slogan,
									startTime: this.customFormData.startTime,
									endTime: this.customFormData.endTime
								},
								success: (res2) => {
									console.log(res2);
									uni.navigateBack()
									uni.showToast({
										title: `提交成功`,
										mask:true
									})
								}
							})
						}
					}
				})
			},
			checkTime(i) {
				if (i < 10) {
					i = "0" + i;
				}
				return i;
			},
			changeTime(date) {
				var nowdate = new Date(date * 1)
				var year = nowdate.getFullYear(),
					month = nowdate.getMonth() + 1,
					date = nowdate.getDate(),
					day = nowdate.getDay(),
					h = nowdate.getHours(),
					m = nowdate.getMinutes(),
					s = nowdate.getSeconds(),
					h = this.checkTime(h),
					m = this.checkTime(m),
					s = this.checkTime(s);
				return year + "-" + month + "-" + date + " " + h + ":" + m + ":" + s;
			},
			//照片文件如何上传...待处理
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[0].name, e.tempFilePaths[0], e.tempFiles[0]
						.fileType, "se-activity")
					.then((res) => {
						console.log('success', res)
						_this.customFormData.picture = []
						let temp = {
							"url": res
						}
						_this.customFormData.picture.push(temp);
					})
			},
		}
	}
</script>

<style>
	.form {
		margin-top: 50rpx;
	}

	.myButton {
		width: 90vw;
		margin: 1vh auto;
		background-image: linear-gradient(to right, #FF8008 0%, #FFC837 51%, #FF8008 100%);
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block;

	}

	.myButton:hover {
		background-position: right center;
		/* change the direction of the change here */
		color: #fff;
		text-decoration: none;
	}
</style>
