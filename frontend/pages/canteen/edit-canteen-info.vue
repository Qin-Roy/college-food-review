<!-- 编辑食堂信息表单 -->
<template>
	<view class="edit-canteen-info">
		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules" :modelValue="canteenInfo">
				<uni-section title="食堂信息" type="line">
					<uni-forms-item label="食堂名称" required name="name">
						<uni-easyinput maxlength="20" v-model="canteenInfo.nickName" placeholder="请输入食堂名称" />
					</uni-forms-item>
					<uni-forms-item label="食堂地址" required name="address">
						<uni-easyinput maxlength="40" v-model="canteenInfo.address" placeholder="请输入食堂地址" />
					</uni-forms-item>
					<uni-forms-item label="联系电话" required name="telephone">
						<uni-easyinput type="number" maxlength="15" v-model="canteenInfo.tel" placeholder="请输入联系电话" />
					</uni-forms-item>
					<uni-forms-item label="食堂介绍" required name="introduction">
						<uni-easyinput type="textarea" maxlength="50" v-model="canteenInfo.intro" placeholder="请输食堂介绍" />
					</uni-forms-item>
					<uni-forms-item label="店铺分类" required name="categoryList">
						<uni-data-checkbox v-model="canteenInfo.categoryList" multiple :localdata="totalCategory" />
					</uni-forms-item>
				</uni-section>
				<uni-section title="头像选择" type="line" required>
					<uni-file-picker auto-upload="false" @select="selectAvatar" v-model="canteenInfo.avatarUrl"
						file-mediatype="image" returnType="object" limit="1" title="最多选择1张图片"></uni-file-picker>
					<text v-if="(canteenInfo.avatarUrl.length == 0) || (canteenInfo.avatarUrl == null)"
							style="color: red;">请选择一张照片</text>
				</uni-section>
				<uni-section title="照片列表" type="line">
					<uni-file-picker auto-upload="false" @select="selectImage" @delete="deleteImage"
						v-model="canteenInfo.pictureList" file-mediatype="image" limit="3" title="最多选择3张图片">
					</uni-file-picker>
				</uni-section>
				</uni-section>

			</uni-forms>
			<view class="myButtonArea">
				<button class="myButton" @click="submit('customForm','store-homepage')">提交</button>
			</view>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				userInfo: {},
				canteenId: "",
				userId: "",
				status: "",
				canteenInfo: {
					nickName: "",
					avatarUrl: [],
					schoolId: "",
					address: "",
					tel: "",
					intro: "",
					pictureList: [],
					tmp_pictureList: [],
					categoryList: [],
					tmp_categoryList: []
				},
				totalCategory: [],
				customRules: {
					name: {
						rules: [{
							required: true,
							errorMessage: '食堂名称不能为空'
						}]
					},
					address: {
						rules: [{
							required: true,
							errorMessage: '食堂地址不能为空'
						}]
					},
					telephone: {
						rules: [{
							required: true,
							errorMessage: '联系电话不能为空'
						}]
					},
					introduction: {
						rules: [{
							required: true,
							errorMessage: "食堂介绍不能为空"
						}]
					},
					categoryList: {
						rules: [{
							required: false,
							errorMessage: "食堂标签不能为空"
						}]
					}
				}
			}
		},
		onShow() {

		},
		onLoad(option) {
			console.log(option)
			this.canteenId = option.id
			this.userId = option.userId
			this.getSchoolInfo()
			this.getTotalCategoryList()
			if (this.canteenId) {
				this.getCanteenInfo()
				console.log("更新")
				this.status = 1
			} else {
				this.status = 0
			}
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					this.userInfo = res.data;
				}
			})
		},
		methods: {
			getSchoolInfo() {
				let that = this
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						console.log(res.data.schoolId)
						that.canteenInfo.schoolId = res.data.schoolId
					}
				})
			},
			getTotalCategoryList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getCategory',
					method: 'GET',
					success: (res) => {
						console.log(res.data.data.TotalCategoryList)
						// this.totalCategory = res.data.data.TotalCategoryList
						let len = res.data.data.TotalCategoryList.length
						for (let i = 0; i < len; i++) {
							let category = new Object();
							category.text = res.data.data.TotalCategoryList[i];
							category.value = i;
							console.log(category);
							this.totalCategory.push(category);
						}
						// console.log(this.totalCategory)
					}
				})
			},
			getCanteenInfo() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getOneCanteenInfo',
					data: {
						userId: this.userInfo.id,
						canteenId: this.canteenId
					},
					method: 'GET',
					success: (res) => {
						let canteen = res.data.data
						console.log(res.data.data)
						console.log(this.totalCategory)
						if (canteen.avatarUrl != "") {
							let temp = {
								"url": canteen.avatarUrl
							}
							this.canteenInfo.avatarUrl.push(temp);
						}
						this.canteenInfo.address = canteen.address

						for (let i = 0; i < canteen.categoryList.length; i++) {
							this.canteenInfo.categoryList.push(Number(canteen.categoryList[i]) -
								2) // 为了使得前后端categoryList数据相对应
							// console.log(cate)
						}
						console.log(canteen.categoryList)
						console.log(this.canteenInfo.categoryList)
						this.canteenInfo.intro = canteen.intro
						this.canteenInfo.nickName = canteen.nickName
						this.canteenInfo.tmp_pictureList = canteen.pictureList
						this.canteenInfo.tel = canteen.tel
						for (let i = 0; i < canteen.pictureList.length; i++) {
							let temp = {
								"url": canteen.pictureList[i]
							}
							this.canteenInfo.pictureList.push(temp);
						}
					}
				})
			},
			submit(ref, url) {
				this.$refs[ref].validate().then(res => {
					console.log('success', res)
					this.submitForm(res)
				}).catch(err => {
					console.log('err', err);
				})
			},
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.canteenInfo.pictureList = []
				for (let i = 0; i < _this.canteenInfo.tmp_pictureList.length; i++) {
					let temp = {
						"url": _this.canteenInfo.tmp_pictureList[i]
					}
					_this.canteenInfo.pictureList.push(temp);
				}
				for (let k = 0; k < e.tempFilePaths.length; k++) {
					_this.$OBS.myUploadFile(e.tempFiles[k].name, e.tempFilePaths[k], e.tempFiles[k].fileType, "se-canteen")
						.then((res) => {
							console.log('success', res)
							// _this.canteenInfo.pictureList.push(res)
							_this.canteenInfo.tmp_pictureList.push(res)
							_this.canteenInfo.pictureList = []
							for (let i = 0; i < _this.canteenInfo.tmp_pictureList.length; i++) {
								let temp = {
									"url": _this.canteenInfo.tmp_pictureList[i]
								}
								_this.canteenInfo.pictureList.push(temp);
							}
						})
				}
			},
			selectAvatar(e) {
				let _this = this
				console.log('选择图片', e)
				_this.$OBS.myUploadFile(e.tempFiles[0].name, e.tempFilePaths[0], e.tempFiles[0].fileType, "se-user-avatar")
					.then((res) => {
						console.log('success', res)
						_this.canteenInfo.avatarUrl = []
						let temp = {
							"url": res
						}
						_this.canteenInfo.avatarUrl.push(temp);
					})
			},
			deleteImage(e) {
				console.log(e)
				console.log(this.canteenInfo.pictureList)
				for (let i = 0; i < this.canteenInfo.tmp_pictureList.length; i++) {
					if (this.canteenInfo.tmp_pictureList[i] == e.tempFilePath) {
						this.canteenInfo.tmp_pictureList.splice(i, 1)
					}
				}
			},
			submitForm(res) {
				console.log(res)
				let categoryList = []
				console.log(res.categoryList)
				for (let i = 0; i < res.categoryList.length; i++) {
					// 为了使得前后端categoryList数据相对应 要加上2
					categoryList.push((res.categoryList[i] + 2).toString())
					res.categoryList[i] = res.categoryList[i] + 2
				}
				if (this.status == 1) {
					console.log("changeCanteenInfo" + this.canteenId)
					console.log(this.canteenInfo.avatarUrl)
					// let categoryList = []
					// console.log(res.categoryList)
					// for(let i = 0; i < res.categoryList.length; i++) {
					// 	categoryList.push(res.categoryList[i].toString())
					// }
					console.log(categoryList)
					uni.request({
						url: 'http://124.71.170.100/index.php/index/editInfo',
						header: {
							"content-type": "application/json"
						},
						data: {
							nickName: res.name,
							id: this.canteenId,
							// avatarUrl: "balabal",
							avatarUrl: this.canteenInfo.avatarUrl == null ? "" : this.canteenInfo.avatarUrl[0].url,
							intro: res.introduction,
							tel: res.telephone,
							address: res.address,
							categoryList: res.categoryList,
							pictureList: this.canteenInfo.tmp_pictureList,
							isCanteen: "1"
						},
						method: 'POST',
						success: (res1) => {
							console.log(res1)
							// console.log(res1.data.data.canteenId)
							uni.navigateBack({
								url: 'canteenList'
							})
							uni.showToast({
								mask: true,
								title: `提交成功`
							})
						}
					})
				} else {
					console.log(this.canteenInfo.schoolId + " 学校id")
					console.log(categoryList)
					uni.request({
						url: 'http://124.71.170.100/index.php/index/createCanteen',
						header: {
							"content-type": "application/json",
						},
						data: {
							name: res.name,
							avatarUrl: this.canteenInfo.avatarUrl == null ? "" : this.canteenInfo.avatarUrl[0].url,
							intro: res.introduction,
							schoolId: this.canteenInfo.schoolId,
							tel: res.telephone,
							address: res.address,
							categoryList: categoryList,
							pictureList: this.canteenInfo.tmp_pictureList,
						},
						method: 'POST',
						success: (res1) => {
							console.log(res1.data.code)
							console.log(res1.data.data.canteenId)
							uni.navigateBack({
								url: 'canteenList'
							})
							uni.showToast({
								mask: true,
								title: `提交成功`
							})
						}
					})
				}
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
		background: #FFC837;
	}
</style>
