<template>
	<view style="height: 100vh;">
		<uni-card title="身份认证" subTitle="选择后就不能轻易更改了哦" shadow="true">
			<uni-forms ref="form" :modelValue="formData" style="height: 80vh;">

				<uni-section title="选择身份" type="line">
					<uni-forms-item name="character">
						<uni-combox mptyTips="这里啥都没有" :candidates="characterSelected" placeholder="请选择您的身份"
							v-model="formData.character"></uni-combox>
					</uni-forms-item>
				</uni-section>

				<!-- <uni-section v-if="formData.character == '商家' || formData.character == '管理员'" title="佐证资料"
					subTitle="图片/视频/文档等任意形式文件" type="line">
					<uni-forms-item name="files">
						<uni-file-picker ref="files" :autoUpload="false" limit="3" file-mediatype="all" title="最多选择3个文件"
							@select="actionsClick('选择成功')"></uni-file-picker>
					</uni-forms-item>
				</uni-section> -->

			</uni-forms>

			<button class="gonButtonInform submit" @click="submitForm()">确定</button>
		</uni-card>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: "",
				formData: {
					character: ""
				},
				characterSelected: [
					"普通用户",
					"商家",
					"管理员"
				]
			}
		},
		onLoad: function(option) {
			this.id = option.openid;
		},
		methods: {
			submitForm() {
				let that = this;
				uni.request({
					url: 'http://124.71.170.100/index.php/index/register',
					method: "POST",
					header: {
						"content-type": "application/x-www-form-urlencoded",
					},
					data: {
						"openid": this.id,
						"identity": this.formData.character == "普通用户" ? "1" : (this.formData.character == "管理员" ?
							"0" : "2"),
					},
					success: (res) => {
						console.log('提交成功' + res);
						uni.getStorage({
							key: 'userInfo',
							success: (res2) => {
								uni.setStorage({
									key: 'userInfo',
									data: {
										id: res2.data.id,
										nickName: res2.data.nickName,
										identity: that.formData.character == "普通用户" ? "1" :
											(that.formData.character == "管理员" ? "0" : "2"),
										status: res2.data.status,
										schoolName: res2.data.schoolName,
										schoolId: res2.data.schoolId,
										avatarUrl: res2.data.avatarUrl,
										favouredStoreNum: res2.data.favouredStoreNum,
										followerNum: res2.data.followerNum,
										followingNum: res2.data.followingNum
									},
									success() {
										uni.reLaunch({
											url: '../index/index'
										});
									}
								})
							}
						})
					},
					fail: () => {
						console.log('提交失败');
					}
				})
				console.log("选择身份：");
				console.log(this.formData.character == "普通用户" ? "1" : "2");
				console.log("表单提交");
			},
			upload() {
				this.$refs.files.upload() //文件手动提交
			},
			selectedWell() {
				console.log("选择成功")
			},
			actionsClick(text) {
				uni.showToast({
					title: text,
					icon: 'none'
				})
			}
		}
	}
</script>

<style>
	button.submit {
		text-align: center;
		width: 35vw;
		margin: 5vh auto;
	}

	.gonButtonInform {
		background-color: #FFC837;
		/* background-color: #47b6ff; */
	}
</style>
