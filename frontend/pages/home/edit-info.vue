<template>
	<!--修改个人信息-->
	<view class="container">
		<uni-section title="我的信息" type="line">
			<view class="changeinfo">
				<image class="headphoto" :src="avatarUrl" @click="changeImage"></image>
				<uni-forms ref="valiForm" :rules="rules">
					<uni-forms-item label="昵称" required name="name">
						<uni-easyinput v-model="nickName" placeholder="请输入昵称" maxlength="10" />
						<view class="num">{{ nickName.length }}/10</view>
					</uni-forms-item>
				</uni-forms>
				<button type="primary" @click="submit('valiForm')">提交</button>
			</view>
		</uni-section>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				userInfo: {},
				id: '',
				avatarUrl: '',
				nickName: "",
				// 校验规则
				rules: {
					name: {
						rules: [{
							required: true,
							errorMessage: '昵称不能为空'
						}]
					},
				},
			}
		},
		onLoad: function(option) { //option为object类型，会序列化上个页面传递的参数
			this.id = option.id;
			this.avatarUrl = option.avatarUrl;
			this.nickName = option.nickName;
			console.log("用户id：" + this.id);
		},
		methods: {
			submit(ref) {
				let that = this;
				this.$refs[ref].validate().then(res => {
					console.log('success', res);
					uni.request({
						url: 'http://124.71.170.100/index.php/index/editProfile',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							avatarUrl: this.avatarUrl,
							nickName: this.nickName,
							id: this.id,
						},
						success: (res) => {
							console.log("修改信息成功");
							console.log(res);
							console.log(this.avatarUrl);
							console.log(this.nickName);
							console.log(this.id);
							uni.getStorage({
								key: 'userInfo',
								success: (res) => {
									uni.setStorage({
										key: 'userInfo',
										data: {
											id: res.data.id,
											nickName: that.nickName,
											identity: res.data.identity,
											status: res.data.status,
											schoolName: res.data.schoolName,
											schoolId: res.data.schoolId,
											avatarUrl: that.avatarUrl,
											favouredStoreNum: res.data.favouredStoreNum,
											followerNum: res.data.followerNum,
											followingNum: res.data.followingNum
										},
										success() {
											uni.navigateBack({
												url: '/pages/home/homepage'
											});
											uni.showToast({
												title: `保存成功！`,
											})
										}
									})
								}
							})
						}
					})

				}).catch(err => {
					console.log('err', err);
				})
			},
			changeImage() {
				uni.chooseImage({
					count: 1,
					sizeType: ['copressed'],
					success: (res) => {
						//因为有一张图片， 输出下标[0]， 直接输出地址
						var imgFiles = res.tempFilePaths[0]
						console.log(res)
						// let key = imgFiles.split('/')[3]
						let key = (new Date().getTime()).toString() + Math.floor(Math.random()*10000+1)
						console.log(key)
						this.$OBS.myUploadFile(key, imgFiles, "image", "se-user-avatar")
							.then((res) => {
								console.log('success', res)
								this.avatarUrl = res;
							})

					}
				})

			}
		},
	}
</script>

<style lang="scss">
	.container {
		background-color: aliceblue;
	}

	.headphoto {
		width: 180rpx;
		height: 180rpx;
		border-radius: 50%;
		border: 3px solid #fff;
		margin-bottom: 40rpx;
	}

	.changeinfo {
		padding: 15px;
		background-color: #fff;
	}

	.form-item {
		display: flex;
		align-items: center;
	}

	.button {
		display: flex;
		align-items: center;
		height: 35px;
		margin-left: 10px;
	}

	.num {
		text-align: right;
		color: gray
	}
</style>
