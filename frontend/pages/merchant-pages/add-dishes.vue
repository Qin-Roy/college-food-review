<!--商家添加菜品页面-->
<template>
	<view class="add-dishes">

		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules" :modelValue="dishData">
				<uni-section title="菜品信息" type="line">
					<uni-forms-item label="菜品名称" required name="name">
						<uni-easyinput maxlength="15" v-model="dishData.name" placeholder="请输入菜品名称" />
					</uni-forms-item>
					<uni-forms-item label="菜品价格" required name="price">
						<uni-easyinput maxlength="6" type="digit" v-model="dishData.price" placeholder="请输入菜品价格" />
					</uni-forms-item>
					<uni-forms-item label="菜品介绍" required name="intro">
						<uni-easyinput maxlength="70" type="textarea" v-model="dishData.intro" placeholder="请输菜品介绍" />
					</uni-forms-item>
				</uni-section>

				<uni-section title="菜品照片" type="line">
					<uni-file-picker auto-upload="false" @select="selectImage" @delete="deleteImage"
						v-model="dishData.images" file-mediatype="image" limit="3" title="最多选择3张图片">
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
				id: "",
				userInfo: "",
				status: "",
				dishData: {
					dishId: "",
					name: "",
					price: "",
					intro: "",
					images: [],
					tmp_images: []
				},
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
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data;
					that.status = option.status;
					if (option.status == 1) {
						uni.request({
							url: 'http://124.71.170.100/index.php/index/getSingleDishInfo',
							method: "GET",
							data: {
								dishId: option.id,
								isCanteen: "0"
							},
							success: (res) => {
								console.log(res)
								this.dishData.dishId = res.data.data.dishId;
								this.dishData.name = res.data.data.dishName;
								this.dishData.intro = res.data.data.intro;
								this.dishData.price = res.data.data.price;
								this.dishData.tmp_images = res.data.data.pictureList;

								for (let i = 0; i < res.data.data.pictureList
									.length; i++) {
									let temp = {
										"url": res.data.data.pictureList[i]
									}
									this.dishData.images.push(temp);
								}

							}
						})
					}
				}
			})
		},
		methods: {

			submit(ref, url) {
				let that = this;
				this.$refs[ref].validate().then(res => {
					if (that.status == 1) {
						uni.request({
							url: 'http://124.71.170.100/index.php/index/editDish',
							method: 'POST',
							header: {
								"content-type": "application/json"
							},
							data: {
								dishName: this.dishData.name,
								intro: this.dishData.intro,
								price: this.dishData.price.toString(),
								pictureList: this.dishData.tmp_images,
								dishId: this.dishData.dishId,
								isCanteen: "0"
							},
							success: (res2) => {
								console.log(res2)
								uni.navigateBack()
								uni.showToast({
									title: `提交成功`,
									mask:true
								})
							}
						})
					} else {
						uni.request({
							url: 'http://124.71.170.100/index.php/index/addDish',
							method: 'POST',
							header: {
								"content-type": "application/json"
							},
							data: {
								dishName: this.dishData.name,
								intro: this.dishData.intro,
								price: this.dishData.price,
								pictureList: this.dishData.tmp_images,
								merchantId: this.userInfo.id,
								isCanteen: "0"
							},
							success: (res2) => {
								console.log(res2)
								uni.navigateBack()
								uni.showToast({
									title: `提交成功`,
									mask:true
								})
							}
						})
					}
				}).catch(err => {
					console.log('err', err);
				})
			},
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.dishData.images = []
				for (let i = 0; i < _this.dishData.tmp_images.length; i++) {
					let temp = {
						"url": _this.dishData.tmp_images[i]
					}
					_this.dishData.images.push(temp);
				}
				for (let k = 0; k < e.tempFilePaths.length; k++) {
					_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[k].name, e.tempFilePaths[k], e.tempFiles[k]
							.fileType, "se-store-dish")
						.then((res) => {
							console.log('success', res)
							// _this.dishData.images.push(res)
							_this.dishData.tmp_images.push(res)
							_this.dishData.images = []
							for (let i = 0; i < _this.dishData.tmp_images.length; i++) {
								let temp = {
									"url": _this.dishData.tmp_images[i]
								}
								_this.dishData.images.push(temp);
							}
						})
				}
			},
			deleteImage(e) {
				console.log(e)
				console.log(this.dishData.images)
				for (let i = 0; i < this.dishData.tmp_images.length; i++) {
					if (this.dishData.tmp_images[i] == e.tempFilePath) {
						this.dishData.tmp_images.splice(i, 1)
					}
				}
			}
		},

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
