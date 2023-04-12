<!--商家添加菜品页面-->
<template>
	<view class="add-dishes">

		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules">
				<uni-section title="菜品信息" type="line">
					<uni-forms-item label="菜品名称" required name="name">
						<uni-easyinput v-model="dishName" maxlength="20" placeholder="请输入菜品名称" />
					</uni-forms-item>
					<uni-forms-item label="菜品价格" required name="price">
						<uni-easyinput type="number" maxlength="6" v-model="price" placeholder="请输入菜品价格" />
					</uni-forms-item>
					<uni-forms-item label="菜品介绍" required name="intro">
						<uni-easyinput type="textarea" maxlength="50" v-model="intro" placeholder="请输菜品介绍" />
					</uni-forms-item>
				</uni-section>

				<uni-section title="菜品照片" type="line">
					<uni-file-picker auto-upload="false" @select="selectImage" @delete="deleteImage"
						v-model="pictureList" file-mediatype="image" limit="3" title="最多选择3张图片">
					</uni-file-picker>
				</uni-section>

			</uni-forms>
			<view class="myButtonArea">
				<button class="myButton" @click="submit('customForm','/store-homepage')">提交</button>
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				merchantId: "",
				userId: "",
				dishName: "",
				price: "",
				intro: "",
				pictureList: [],
				tmp_pictureList: [],
				customRules: {
					name: {
						rules: [{
							required: true,
							errorMessage: '菜品名称不能为空'
						}]
					},
					price: {
						rules: [{
							required: true,
							errorMessage: '菜品价格不能为空'
						}]
					},
					intro: {
						rules: [{
							required: true,
							errorMessage: "菜品介绍不能为空"
						}]
					}
				}
			}
		},
		onLoad: function(option) {
			let that = this;
			this.merchantId = option.merchantId;
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					console.log(res.data);
					that.userId = res.data.id;
				}
			});

		},
		methods: {
			submit(ref, url) {
				this.$refs[ref].validate().then(res => {
					console.log('success', res);
					uni.request({
						url: 'http://124.71.170.100/index.php/index/postdishUpdateRequest',
						method: 'POST',
						header: {
							"content-type": "application/json"
						},
						data: {
							merchantId: this.merchantId,
							userId: this.userId,
							dishName: this.dishName,
							price: this.price,
							intro: this.intro,
							pictureList: this.tmp_pictureList
						},
						success: (res2) => {
							console.log(res2)
							uni.navigateBack({
									url: '/pages/index/store'
								}),
								uni.showToast({
									title: `提交成功`
								})
						}
					})
				}).catch(err => {
					console.log('err', err);
				})
			},
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.pictureList = []
				for (let i = 0; i < _this.tmp_pictureList.length; i++) {
					let temp = {
						"url": _this.tmp_pictureList[i]
					}
					_this.pictureList.push(temp);
				}
				for (let k = 0; k < e.tempFilePaths.length; k++) {
					_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[k].name, e.tempFilePaths[k], e.tempFiles[k]
							.fileType, "se-canteen-dish")
						.then((res) => {
							console.log('success', res)
							// _this.dishData.images.push(res)
							_this.tmp_pictureList.push(res)
							_this.pictureList = []
							for (let i = 0; i < _this.tmp_pictureList.length; i++) {
								let temp = {
									"url": _this.tmp_pictureList[i]
								}
								_this.pictureList.push(temp);
							}
						})
				}
			},
			deleteImage(e) {
				console.log(e)
				console.log(this.pictureList)
				for (let i = 0; i < this.tmp_pictureList.length; i++) {
					if (this.tmp_pictureList[i] == e.tempFilePath) {
						this.tmp_pictureList.splice(i, 1)
					}
				}
			},
		}
	}
</script>

<style>
	.form {
		margin-top: 50rpx;
	}

	.myButtonArea {
		margin-top: 200rpx;
	}

	,
	.myButton {
		background-color: #FFC837;
	}
</style>
