<!--商家修改信息页面-->
<template>
	<view class="edit-store-info">
		<!--表单界面-->
		<view class="form">
			<uni-forms ref="customForm" :rules="customRules" :modelValue="storeData">
				<uni-section title="商家信息" type="line">
					<uni-forms-item label="商家名称" required name="name">
						<uni-easyinput v-model="storeData.name" maxlength="20" placeholder="请输入商家名称" />
					</uni-forms-item>
					<uni-forms-item label="商家地址" required name="address">
						<uni-easyinput v-model="storeData.address" maxlength="40" placeholder="请输入商家地址" />
					</uni-forms-item>
					<uni-forms-item label="联系电话" required name="telephone">
						<uni-easyinput type="number" maxlength="15" v-model="storeData.telephone"
							placeholder="请输入联系电话" />
					</uni-forms-item>
					<uni-forms-item label="商家介绍" required name="introduction">
						<uni-easyinput type="textarea" maxlength="50" v-model="storeData.intro" placeholder="请输商家介绍" />
					</uni-forms-item>
					<uni-forms-item label="店铺分类" name="catagoryList">
						<uni-data-checkbox v-model="storeData.categoryList" multiple :localdata="categoryLists"
							@change="changeCategory" />
					</uni-forms-item>
					<uni-forms-item label="所属学校" required name="school">
						<uni-combox :candidates="schoolNames" placeholder="选择学校" v-model="storeData.school"></uni-combox>
					</uni-forms-item>
				</uni-section>
				<uni-section title="头像选择" type="line" required>
					<uni-file-picker auto-upload="false" @select="selectAvatar" v-model="storeData.avatar"
						file-mediatype="image" returnType="object" limit="1"></uni-file-picker>
					<text v-if="(storeData.avatar.length == 0) || (storeData.avatar == null)"
						style="color: red;">请选择一张照片</text>
				</uni-section>
				<uni-section title="照片列表" type="line" required>
					<uni-file-picker auto-upload="false" @select="selectImage" @delete="deleteImage"
						v-model="storeData.images" file-mediatype="image" limit="3" title="最多选择3张图片"></uni-file-picker>
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
				schoolNames:[],
				schoolIds:[],
				userInfo: {},
				storeData: {
					name: "",
					avatar: [],
					school:"",
					address: "",
					telephone: "",
					intro: "",
					categoryList: [],
					tmp_categoryList: [],
					images: [],
					tmp_images: []
				},
				categoryLists: [],
				customRules: {
					name: {
						rules: [{
							required: true,
							errorMessage: '商家名称不能为空'
						}]
					},
					school:{
						rules: [{
							required: true,
							errorMessage: '学校不能为空'
						}]
					},
					address: {
						rules: [{
							required: true,
							errorMessage: '商家地址不能为空'
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
							errorMessage: "商家介绍不能为空"
						}]
					}
				}
			}
		},
		onLoad(option) {
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					// that.storeData.name = res.data.data.nickName;
					// that.storeData.address = res.data.data.
					that.userInfo = res.data;
					that.storeData.school = res.data.schoolName;
				}
			})
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getSchoolList',
				method:"GET",
				success: (res) => {
					console.log("获取学校列表",res)
					for(let i = 0; i < res.data.data.schoolList.length; i++){
						this.schoolNames.push(res.data.data.schoolList[i].schoolName);
						this.schoolIds.push(res.data.data.schoolList[i].schoolId)
					}
				}
			})
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getCategory',
				method: 'GET',
				success: (res2) => {
					console.log('获取分类列表', res2.data.data.TotalCategoryList);
					let len = res2.data.data.TotalCategoryList.length;
					// this.categoryLists = res.data.data.TotalCategoryList;
					for (let i = 0; i < len; i++) {
						let category = new Object();
						category.text = res2.data.data.TotalCategoryList[i];
						category.value = i;
						// console.log(category);
						this.categoryLists.push(category);

					}
					uni.request({
						url: 'http://124.71.170.100/index.php/index/getStoreInfo',
						data: {
							id: option.id
						},
						method: 'GET',
						success: (res) => {
							console.log("获取商家当前信息成功" + JSON.stringify(res))
							console.log('获得code ' + res.data.code)
							if (res.data.data.avatarUrl != "") {
								let temp = {
									"url": res.data.data.avatarUrl
								}
								this.storeData.avatar.push(temp);
							}
							this.storeData.name = res.data.data.nickName;
							this.storeData.address = res.data.data.address;
							this.storeData.telephone = res.data.data.tel;
							this.storeData.intro = res.data.data.intro;
							console.log("获得商家名称 " + this.storeData.name);
							console.log("获得商家地址 " + this.storeData.address);
							console.log("获得商家联系方式 " + this.storeData.telephone);
							console.log("获得商家介绍 " + this.storeData.intro);
							this.storeData.tmp_images = res.data.data.pictureList;
							console.log("图片地址 " + this.storeData.images);
							console.log('获取分类列表' + res.data.data.categoryList);
							this.storeData.tmp_categoryList = res.data.data.categoryList;
							for (let i = 0; i < res.data.data.categoryList.length; i++) {
								for (let j = 0; j < this.categoryLists.length; j++) {
									if (res.data.data.categoryList[i] == this.categoryLists[j]
										.text) {
										this.storeData.categoryList.push(this.categoryLists[j]
											.value)
										break
									}
								}
							}
							for (let i = 0; i < res.data.data.pictureList.length; i++) {
								let temp = {
									"url": res.data.data.pictureList[i]
								}
								this.storeData.images.push(temp);
							}
						}
					})
				}
			})
		},
		onShow() {
			wx.hideHomeButton();
		},
		methods: {
			submit(ref, url) {
				if ((this.storeData.avatar != null) && (this.storeData.avatar.length != 0)) {
					this.$refs[ref].validate().then(res => {
						console.log('success', res);
						uni.request({
							url: 'http://124.71.170.100/index.php/index/editInfo',
							method: 'POST',
							header: {
								"content-type": "application/json",
							},
							data: {
								nickName: this.storeData.name,
								id: this.userInfo.id,
								avatarUrl: this.storeData.avatar == null || this.storeData.avatar.length ==
									0 ?
									"" : this.storeData.avatar[0].url,
								categoryList: this.storeData.tmp_categoryList,
								address: this.storeData.address,
								tel: this.storeData.telephone,
								intro: this.storeData.intro,
								pictureList: this.storeData.tmp_images,
								isCanteen: "0",
								schoolName: this.storeData.school
							},
							success: (res2) => {
								// res2.data.nickName = res.name;
								// res2.data.address = res.address;
								// res2.data.tel = res.telephone;
								console.log(res2)
								uni.reLaunch({
									url: './store-homepage'
								});
								uni.showToast({
									title: `提交成功`,
									mask: true
								})
							}
						})
					}).catch(err => {
						console.log('err', err);
					})
				}
			},
			selectImage(e) {
				let _this = this
				console.log('选择图片', e)
				_this.storeData.images = []
				for (let i = 0; i < _this.storeData.tmp_images.length; i++) {
					let temp = {
						"url": _this.storeData.tmp_images[i]
					}
					_this.storeData.images.push(temp);
				}
				for (let k = 0; k < e.tempFilePaths.length; k++) {
					_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[k].name, e.tempFilePaths[k], e.tempFiles[k]
							.fileType, "se-store")
						.then((res) => {
							console.log('success', res)
							// _this.storeData.images.push(res)
							_this.storeData.tmp_images.push(res)
							_this.storeData.images = []
							for (let i = 0; i < _this.storeData.tmp_images.length; i++) {
								let temp = {
									"url": _this.storeData.tmp_images[i]
								}
								_this.storeData.images.push(temp);
							}
						})
				}
			},
			selectAvatar(e) {
				let _this = this
				console.log('选择图片', e)
				_this.$OBS.myUploadFile(new Date().getTime() + e.tempFiles[0].name, e.tempFilePaths[0], e.tempFiles[0]
						.fileType, "se-user-avatar")
					.then((res) => {
						console.log('success', res)
						_this.storeData.avatar = []
						let temp = {
							"url": res
						}
						_this.storeData.avatar.push(temp);
					})
			},
			deleteImage(e) {
				console.log(e)
				console.log(this.storeData.images)
				for (let i = 0; i < this.storeData.tmp_images.length; i++) {
					if (this.storeData.tmp_images[i] == e.tempFilePath) {
						this.storeData.tmp_images.splice(i, 1)
					}
				}
			},
			changeCategory() {
				this.storeData.tmp_categoryList = [];
				for (let i = 0; i < this.storeData.categoryList.length; i++) {
					this.storeData.tmp_categoryList.push(this.categoryLists[this.storeData.categoryList[i]].text)
				}
			}
		}
	}
</script>

<style>
	.form {
		margin-top: 50rpx;
	}

	.myButton {
		width: 60vw;
		margin: 2vh auto;
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
