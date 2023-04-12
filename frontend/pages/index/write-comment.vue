<!--写评论页面-->
<template>
	<view class="writeComment">
		<!--表单界面-->
		<view class="form">
		<uni-forms ref="Form" :rules="Rules">
			<uni-section title="写评价" type="line">
			<view style="display: flex;margin-bottom: 20rpx;">
				评分：
				<uni-rate size="30" allow-half v-model="score" />
			</view>
			<uni-forms-item label="评论" required name="content">
				<uni-easyinput trim="all" type="textarea" maxlength="100" v-model="content" placeholder="请输入评论" />
				<view class="num">{{ content.length }}/100</view>
			</uni-forms-item>
			</uni-section>	
		</uni-forms>
		<view class="myButtonArea">
		    <button class="myButton"  @click="submit('Form','/store-homepage')">发布评论</button>
		</view>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				dishId: "",
				userId: "",
				time: "",
				content: "",
				score: "",
				isCanteen: "",
				scoreString:"",
				Rules:{
					content:{
						rules:[{
							required:true,
							errorMessage:'评价内容不能为空'
						}]
					}
				}	
			}
		},
		onLoad: function (option){
			let that = this;
			this.dishId=option.dishId;
			this.isCanteen=option.isCanteen;
			uni.getStorage({
				key:'userInfo',
				success: function (res) {
					console.log(res.data);
					that.userId = res.data.id;
				}
			});
			
		},
		methods: {
			remove(e){
				console.log(e)
			},
			submit(ref, url) {
				//发表评论
				this.$refs[ref].validate().then(res => {
					console.log('success', res);
					// this.time=this.getTime();
					this.time=new Date().getTime()
					this.scoreString=this.score.toString();
					uni.request({
						url: 'http://124.71.170.100/index.php/index/postComment',
						method: 'POST',
						header:{
							'content-type':"application/json"
						},
						data:{
							dishId: this.dishId,
							isCanteen: this.isCanteen,
							id: this.userId,
							time: this.time,
							content: this.content,
							score: this.scoreString,
						},
						success: (res2) => {
							console.log(res2)
							console.log(this.dishId)
							console.log(this.isCanteen)
							console.log(this.userId)
							console.log(this.time)
							console.log(this.content)
							console.log(this.scoreString)
							uni.navigateBack({
								url: '/pages/index/dish'
							}),
							uni.showToast({
								title: `发布成功`
							})
						}
					})
				}).catch(err => {
					console.log('err', err);
		        })
		    },
		}
	}
</script>

<style>
	.writeComment{
		font-size: 30rpx;
	}
	.form{
		margin-top: 50rpx;
	}
	.myButtonArea{
		margin-top: 100rpx;
	},
	.myButton{
		background-color: #d4d2b6;
	}
	.num{
		text-align: right;
		color: gray
	}

</style>
