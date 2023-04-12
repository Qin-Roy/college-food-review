<template>
	<view class="login">
		<image class="logo" src="../../static/logo.png"></image>
		<view class="text-area">
			<text class="nickname">{{nickname}}</text>
		</view>
		<button class="myButton" @click="login">微信授权一键登录</button>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				nickname: '',
				result:""
			}
		},
		methods: {
			login() {
				console.log("login")
				uni.getUserProfile({
					desc: 'Weixin', // 必须包含该参数，其数值无所谓
					success: (res) => {
						console.log("用户信息：" + JSON.stringify(res.userInfo));
						let userInfo = res.userInfo;
						uni.login({
							provider: 'weixin',
							success: (res2) => {
								uni.showLoading({
									title: '登陆中'
								})

								var code = res2.code;
								console.log("获取code成功：" + JSON.stringify(res2));
								uni.request({
									url: 'http://124.71.170.100/index.php/index/getopenid',
									method: 'POST',
									header: {
										"content-type": "application/x-www-form-urlencoded"
									},
									data: {
										"code": code,
									},
									success: (res3) => {
										console.log('获取openid成功：');
										this.result = res3;
										console.log(res3.data.data.id);
										uni.hideLoading();
										uni.request({
											url: 'http://124.71.170.100/index.php/index/login',
											method: 'POST',
											header: {
												"content-type": "application/x-www-form-urlencoded",
											},
											data: {
												"openid": res3.data.data.id,
												"nickName": res.userInfo.nickName,
												"avatarUrl":res.userInfo.avatarUrl
											},
											success: (res4) => {
												console.log('获取用户信息成功');
												console.log(res4);
												if (res4.data.data.identity == '4') { // 未认证身份
													uni.setStorage({
														key: 'userInfo',
														data: res4.data.data,
														success: () => {
															uni.reLaunch({
																url:'./register?openid=' + res3.data.data.id
															});
														}
													});
												} else if(res4.data.data.status == '0'){ 
													uni.reLaunch({
														url:'../home/appeal?id=' + res4.data.data.id
													})
												} else {
													uni.setStorage({
														key: 'userInfo',
														data: res4.data.data,
														success: () => {
															uni.reLaunch({
																url: '../index/index'
															});
														}
													});
												}
											},
											fail() {
												console.log("获取用户信息失败");
											}
										})
									},
									fail() {
										uni.showToast({
											title: '登录失败',
											icon: 'none'
										})
									}
								})
							},
						})
					},
					fail() {
						console.log('failed')
					}
				})
			}
		}
	}
</script>


<style>
	.login {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
	}

	.logo {
		height: 150rpx;
		width: 150rpx;
		margin-top: 300rpx;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 50rpx;
	}

	.text-area {
		display: flex;
		justify-content: center;
	}

	.nickname {
		font-size: 36rpx;
		color: #8f8f94;
	}

	.login-button {
		width: 75%;
		margin-top: 40rpx;
		border: 3rpx solid #000000;
		border-radius: 25rpx;

	}
	.myButton {
		width: 60vw;
		margin: 2vh auto;
		background: #FFC837;
		/* background-image: linear-gradient(to right, #FF8008 0%, #FFC837 51%, #FF8008 100%); */
		/* text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee; */
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
