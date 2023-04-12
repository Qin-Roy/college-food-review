<template>
	<!--添加好友-->
	<view class="container">
		<uni-section title="添加好友" type="line">
			<view class="addfriend">
				<view style="margin-bottom: 30rpx;">
					好友昵称：{{nickName}}
				</view>
				<uni-forms ref="valiForm" :rules="rules">
					<uni-forms-item label="备注" required name="remark">
						<uni-easyinput v-model="remark" placeholder="请输入备注" maxlength="10" />
						<view class="num">{{ remark.length }}/10</view>
					</uni-forms-item>
					<uni-forms-item label="添加理由" required name="detail">
						<uni-easyinput v-model="detail" placeholder="请输入理由" maxlength="30" />
						<view class="num">{{ detail.length }}/30</view>
					</uni-forms-item>
				</uni-forms>
				<button type="primary" @click="submit('valiForm')">发送申请</button>
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
				detail: '',
				remark: '',
				// 校验规则
				rules: {
					remark: {
						rules: [{
							required: true,
							errorMessage: '备注不能为空'
						}]
					},
					detail: {
						rules: [{
							required: true,
							errorMessage: '理由不能为空'
						}]
					},
				},
			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.id=option.id;
			this.myId=option.myId;
			this.nickName=option.nickName;
		},
		methods: {
			submit(ref) {
				let that = this;
				this.$refs[ref].validate().then(res => {
					console.log('success', res);
					uni.request({
						url: 'http://124.71.170.100/index.php/index/addFriendRequest',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							id: this.myId,
							otherUserId: this.id,
							remark: this.remark,
							detail: this.detail
						},
						success: (res) => {
							console.log(res);
							console.log(this.myId);
							console.log(this.id);
							console.log(this.remark);
							console.log(this.detail);
							uni.navigateBack({
								url: '/pages/home/homepage'
							});
							uni.showToast({
								title: '已发送申请！',
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
	.headphoto{
		width: 180rpx;
		height: 180rpx;
		border-radius:50%;
		border: 3px solid #fff;
		margin-bottom: 40rpx;
	}
	.addfriend {
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