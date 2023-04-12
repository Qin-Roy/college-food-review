<template>
	<!--添加好友-->
	<view class="container">
		<uni-section title="修改备注" type="line">
			<view class="editRemark">
				<view style="margin-bottom: 30rpx;">
					好友昵称：{{nickName}}
				</view>
				<uni-forms ref="valiForm" :rules="rules">
					<uni-forms-item label="备注" required name="remark">
						<uni-easyinput v-model="remark" placeholder="请输入备注" maxlength="10" />
						<view class="num">{{ remark.length }}/10</view>
					</uni-forms-item>
				</uni-forms>
				<button type="primary" @click="submit('valiForm')">修改</button>
			</view>
		</uni-section>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				myId: '',
				id: '',
				nickName: '',
				remark: '',
				// 校验规则
				rules: {
					remark: {
						rules: [{
							required: true,
							errorMessage: '备注不能为空'
						}]
					},
				},
			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.id=option.id;
			this.myId=option.myId;
			this.nickName=option.nickName;
			this.remark=option.remark;
		},
		methods: {
			submit(ref) {
				let that = this;
				this.$refs[ref].validate().then(res => {
					console.log('success', res);
					uni.request({
						url: 'http://124.71.170.100/index.php/index/changeFriendRemark',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							id: this.myId,
							friendId: this.id,
							remark: this.remark,
						},
						success: (res) => {
							console.log(res);
							console.log(this.myId);
							console.log(this.id);
							console.log(this.remark);
							uni.navigateBack({
								url: '/pages/home/homepage'
							});
							uni.showToast({
								title: '修改成功！',
								duration: 1500
							})
						}
					})
				}).catch(err => {
					console.log('err', err);
				})
			}
		}
	}
</script>

<style lang="scss">
	.container{
		background-color: aliceblue;
	}
	.editRemark {
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
	.num{
		text-align: right;
		color: gray
	}
</style>