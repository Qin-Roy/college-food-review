<!--活动页面-->
<template>
	<view class="activity">
		<uni-nav-bar  shadow="true" fixed="true" :title="store.name" :color="store.textColor" :backgroundColor="store.backgroundColor">
		</uni-nav-bar>
		<uni-notice-bar show-icon scrollable :text="activityInfor.notice" />
		</uni-section>
		
		<swiper indicator-dots="true" autoplay="true" :interval="showSet.intervalTime" :duration="showSet.durationTime" circular="true">
			<swiper-item v-for="(item,index) in activityPic">
				<image class="slideImage" :src="item.url" mode="aspectFit"></image>
			</swiper-item>
    	</swiper>
		
		<uni-section  title="活动名称" type="circle">
			<view class="activityName info">{{activityInfor.name}}</view>
		</uni-section>
		<uni-section title="活动介绍" type="circle">
			<p class="activityInfor info" >{{activityInfor.information}}</p>
		</uni-section>
		<uni-section class="activityTime"  title="活动时间" type="circle">
			<p>{{activityInfor.time}}</p>
		</uni-section>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				current:0,
				activityId:'',
				store:{
					name:"",
					textColor:"#000000",
					backgroundColor:"#ddeaf0"
				},
				activityInfor:{
					name:"",
					notice:"",
					information:"",
					time:"",
					startTime:"",
					endTime:""
				},
				activityPic:[
					{url:""}
				],
				showSet:{
					intervalTime:1000,
					durationTime:500
				}
			}
		},
		onLoad(option){
			this.activityId = option.activityId;
			console.log("活动信息为：",option.activityId)
			// 获取活动
			uni.request({
				url:'http://124.71.170.100/index.php/index/getActivityInfo',
				method:"GET",
				data:{
					activityId: option.activityId
				},
				success: (res) => {
					console.log('获取活动信息成功' + res.data.slogan);
					this.store.name=res.data.merchantNickName;
					this.activityInfor.notice = res.data.slogan;
					this.activityInfor.information = res.data.intro;
					this.activityInfor.startTime = this.changeTime(res.data.startTime);
					this.activityInfor.endTime =  this.changeTime(res.data.endTime);
					this.activityInfor.name = res.data.activityName;
					this.activityPic[0].url = res.data.picture;
					this.activityInfor.time = this.activityInfor.startTime+'~'+this.activityInfor.endTime;// 时间组合
				},
				fail: () => {
					console.log('获取活动信息失败');
				},
			})
		},
		methods: {
			navigateTo(url) {
			  uni.navigateTo({
			    url,
			  });
			},
			clickButton(){
				uni.showToast({
					title:"参与活动",
					icon:"none"
				})
			},
			checkTime(i) {
			    if (i < 10) {
			        i = "0" + i;
			    }
			    return i;
			},
			changeTime (date) {
				var nowdate = new Date(date * 1)
			    var year = nowdate.getFullYear(),
			        month = nowdate.getMonth() + 1,
			        date = nowdate.getDate(),
			        day = nowdate.getDay(),
			        h = nowdate.getHours(),
			        m = nowdate.getMinutes(),
			        s = nowdate.getSeconds(),
			        h = this.checkTime(h),
			        m = this.checkTime(m),
			        s = this.checkTime(s);
			    return year + "-" + month + "-" + date + " " + h +":" + m + ":" + s;
			},
		}
	}
</script>


<style>
	.info {
		white-space: normal;
		word-break: break-word;
		word-warp: break-word;
	}
	.activityButtonArea{
		margin-top: 200rpx;
	},
	.activiyButton{
		background-color:#f0efe0 ;
	}
	.activityName{
		font-size: 50rpx;
		font-style: normal;
		color: #000000;
		text-align:center
	},
	.activityInfor{
		padding: 3rpx;
	}
	.activityTime{
		padding: 3rpx;
	},
	.slideImage{
		width: 100%;
		height: 100%;
	}

</style>
