<!--举报评论页面-->
<template>
	<view>
	<uni-card title="我要举报" :thumbnail="informShow.pictureUrl" subTitle="请如实填写举报信息" :extra="informShow.time" shadow="true">
		<uni-forms ref="form" :modelValue="formData" :rules="rules">
			
			<uni-section :title="informShow.nameTitle" type="line" >
				<text class="nameoShow">{{informShow.content}}</text>
			</uni-section>
			
			<uni-section title="举报理由" type="line">
				<uni-forms-item required name="reason">
					<uni-easyinput maxlength="100" v-model="formData.reason" placeholder="请输入举报理由"></uni-easyinput>
					
				</uni-forms-item>
			</uni-section>
			
			<uni-section title="详细信息" type="line">
				<uni-forms-item required name="detail">
				    <uni-easyinput maxlength="100" type="textarea" v-model="formData.detail" placeholder="请输入举报详细信息"></uni-easyinput>
				</uni-forms-item>
			</uni-section>
			
		</uni-forms>
		
		<button class="gonButtonInform"  @click="submitForm('form')">举报</button>
		<button class="gonButton" @click="concelSubmit()">取消</button>
	</uni-card>

	</view>
</template>

<script>
	export default {
		data() {
			return {
				// 显示的举报信息
				informShow:{
					nameTitle:"被举报评论",
					content:"牛了d撒旦撒",
					pictureUrl:""
				},
				// 提交的举报信息
				formData:{
					commentId:"1",
					time:'00',
					reason:"",
					detail:"",
				},
				// 校验规则
				rules: {
					reason: {
						rules: [{
							required: true,
							errorMessage: '理由不能为空'
						}]
					},
					detail: {
						rules: [{
							required: true,
							errorMessage: '详细信息不能为空'
						}]
					}
				},
			}
		},
		onLoad(option) {
			// 获取评论ID和时间
			this.formData.commentId = option.commentId;
			this.formData.time = this.dateTimeStr('y-m-d h:i');
			// 获取评论信息
			uni.request({
				url:'http://124.71.170.100/index.php/index/getDetailedResponseList',
				method:"GET",	
				data:{
					commentId:option.commentId,
					id:option.id
				},
				success: (res) => {
					 this.informShow.content = res.data.data.content;
				     console.log('获取成功', res);
				},
				fail: () => {
					console.log('获取失败');
				}
			})
		},
		methods:{
			// 提交表单
			submitForm(ref){
				this.$refs[ref].validate().then(res => {
				    uni.request({
					    url:'http://124.71.170.100/index.php/index/reportComment',
					    method:"POST",
						header:{
							'content-type':"application/json"
						},
					    data:{
						    commentId:this.formData.commentId,
						    time:new Date().getTime(),
				    	    reason:this.formData.reason,
						    detail:this.formData.detail,
					    },
					    success: (res) => {
					         console.log('提交成功', res);
							 uni.showToast({
							 	title:'提交成功',
							 	icon:'none'
							 });
							 uni.navigateBack();
					    },
					    fail: () => {
						   console.log('提交失败');
					    }
				    });
				});
			},
			// 将当前时间戳转化为时分秒
			dateTimeStr(str){
				var date = new Date(),
				year = date.getFullYear(), //年
				month = date.getMonth() + 1, //月
				day = date.getDate(), //日
				hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours(), //时
				minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes(), //分
				second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds(); //秒
				month >= 1 && month <= 9 ? (month = "0" + month) : "";
				day >= 0 && day <= 9 ? (day = "0" + day) : "";
				//hour >= 0 && hour <= 9 ? (hour = "0" + hour) : "";
				//minute >= 0 && minute <= 9 ? (minute = "0" + minute) : "";
				//second >= 0 && second <= 9 ? (second = "0" + second) : "";
				if(str.indexOf('y') != -1){
					str = str.replace('y', year)
				}
				if(str.indexOf('m') != -1){
					str = str.replace('m', month)
				}
				if(str.indexOf('d') != -1){
					str = str.replace('d', day)
				}
				if(str.indexOf('h') != -1){
					str = str.replace('h', hour)
				}
				if(str.indexOf('i') != -1){
					str = str.replace('i', minute)
				}
				if(str.indexOf('s') != -1){
					str = str.replace('s', second)
				}
				return str;
			},
			// 取消提交
			concelSubmit(){
				console.log("取消提交")
				uni.showToast({
						title:'取消成功',
						icon:'none'
				})
				setTimeout(()=>{
				uni.navigateBack();
				},500);
			}
		}
	}
</script>

<style>
	.selectBotton{
		size: ;
	}
	.nameoShow{
		color: firebrick;
		margin-left: 50rpx;
		text-align: center;
	}
	.gonButton{
	    background-image: linear-gradient(to right, #fff795 0%, #ffa947  51%, #ffac82  100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;            
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block;
	}
	.gonButtonInform{
	    background-image: linear-gradient(to right, #f4ffb9 0%, #ff7462  51%, #ffb969  100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;            
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block
	}

</style>