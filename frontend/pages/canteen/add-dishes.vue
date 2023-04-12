<!-- 食堂添加、修改和更新菜品 -->
<template>
	<view class="add-dishes">

		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules" :modelValue="dishData">
				<uni-section title="菜品信息" type="line">
					<uni-forms-item label="菜品名称" required name="name">
						<uni-easyinput maxlength="15" v-model="dishData.dishName" placeholder="请输入菜品名称" />
					</uni-forms-item>
					<uni-forms-item label="菜品价格" required name="price">
						<uni-easyinput type="number" maxlength="6" v-model="dishData.price" placeholder="请输入菜品价格" />
					</uni-forms-item>
					<uni-forms-item label="菜品介绍" required name="intro">
						<uni-easyinput type="textarea" maxlength="70" v-model="dishData.intro" placeholder="请输菜品介绍" />
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
				dishId: "",
				userId: "",
				index: "",
				status: "",
				dishData: {
					dishId: "",
					dishName: "",
					price: "",
					score: "",
					intro: "",
					userId: "",
					merchantId: "",
					requestId: "",
					picture: "",
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
			console.log(option)
			this.id = option.id
			this.index = option.index
			this.dishId = option.dishId
			this.status = option.status
			if (option.status == 2) {
				this.getUpdateInfo()
			} else if (option.status == 1) {
				this.getMyInfo()
			} else {

			}

		},
		methods: {
			getMyInfo() {
				let that = this
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						console.log(res)
						that.userId = res.data.id
						console.log(that.userId)
						that.getDishesInfo()
					}
				})
			},
			getUpdateInfo() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getdishUpdateRequest',
					method: 'GET',
					data: {
						canteenId: this.id
					},
					success: (res) => {
						console.log(res.data.data.updateList[this.index])
						this.dishData = res.data.data.updateList[this.index]
						// this.dishData.images = res.data.data.updateList[this.index].pictureList

						this.dishData.tmp_images = res.data.data.updateList[this.index].pictureList
						this.dishData.images = []
						console.log(this.dishData)
						for (let i = 0; i < this.dishData.tmp_images.length; i++) {
							let temp = {
								"url": this.dishData.tmp_images[i]
							}
							this.dishData.images.push(temp)
						}
					}
				})
			},
			getDishesInfo() {
				// uni.request({
				// 	url: 'http://124.71.170.100/index.php/index/getOneCanteenInfo',
				// 	data: {
				// 		userId: this.userId,
				// 		canteenId: this.id
				// 	},
				// 	method: 'GET',
				// 	success: (res) => {
				// 		console.log(res.data.data.dishList[this.index])
				// 		this.dishData = res.data.data.dishList[this.index]
				// 		this.dishData.images = res.data.data.dishList[this.index].picture
				// 		this.dishData.tmp_images = res.data.data.dishList[this.index].picture
				// 	}
				// })
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getSingleDishInfo',
					data: {
						dishId: this.dishId,
						isCanteen: "1",
					},
					method: 'GET',
					success: (res) => {
						this.dishData = res.data.data
						this.dishData.tmp_images = res.data.data.pictureList
						this.dishData.images = []
						console.log(this.dishData)
						for (let i = 0; i < this.dishData.tmp_images.length; i++) {
							let temp = {
								"url": this.dishData.tmp_images[i]
							}
							this.dishData.images.push(temp)
						}
					}
				})
			},
			submit(ref, url) {
				let that = this;
				this.$refs[ref].validate().then(res => {
					if (that.status == 2) {
						that.updateDish(res)
					} else if (that.status == 1) {
						that.editDish(res)
					} else {
						that.addDish(res)
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
							.fileType, "se-canteen-dish")
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
			},
			updateDish(ref) {
				console.log(this.dishData)
				console.log(this.id)
				console.log(this.dishData.pictureList)
				uni.request({
					url: 'http://124.71.170.100/index.php/index/reviewdishUpdateRequest',
					header: {
						"content-type": "application/json"
					},
					data: {
						dishName: this.dishData.dishName,
						userId: this.dishData.userId,
						price: this.dishData.price,
						pictureList: this.dishData.pictureList,
						// pictureList: this.dishData.pictureList,
						intro: this.dishData.intro,
						requestid: this.dishData.requestid,
						merchantId: this.dishData.merchantId
					},
					method: 'POST',
					success: (res2) => {
						uni.navigateBack()
						uni.showToast({
							mask: true,
							title: `提交成功`
						})
					}
				})
			},
			editDish(ref) {
				console.log(ref)
				console.log(this.dishData)
				uni.request({
					url: 'http://124.71.170.100/index.php/index/editDish',
					header: {
						"content-type": "application/json"
					},
					data: {
						dishName: this.dishData.dishName,
						dishId: this.dishData.dishId,
						price: this.dishData.price,
						pictureList: this.dishData.tmp_images,
						intro: this.dishData.intro,
						isCanteen: "1"
					},
					method: 'POST',
					success: (res2) => {
						uni.navigateBack()
						uni.showToast({
							mask: true,
							title: `提交成功`
						})
					}
				})
			},
			addDish(ref) {
				console.log("添加菜品")
				console.log(this.dishData)
				console.log(this.id)
				uni.request({
					url: 'http://124.71.170.100/index.php/index/addDish',
					header: {
						"content-type": "application/json"
					},
					data: {
						dishName: this.dishData.dishName,
						isCanteen: "1",
						price: this.dishData.price,
						pictureList: this.dishData.tmp_images,
						intro: this.dishData.intro,
						merchantId: this.id
					},
					method: 'POST',
					success: (res2) => {
						console.log(res2.data.code)
						uni.navigateBack()
						uni.showToast({
							mask: true,
							title: `提交成功`
						})
					}
				})
			}
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
		/* width: 60vw;
		margin: 2vh auto; */
		background: #FFC837;
		margin: auto 10rpx;
		bottom: 20rpx;
		/* background-image: linear-gradient(to right, #FF8008 0%, #FFC837 51%, #FF8008 100%); */
		/* text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee; */
		/* border-radius: 10px;
		display: block; */

	}

	/* .myButton:hover {
		background-position: right center;
		/* change the direction of the change here */
	/* 	color: #fff;
		text-decoration: none;
	} */
</style>
