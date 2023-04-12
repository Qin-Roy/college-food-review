<template>
	<view>
		<view class="restaurantTitle">
			<text>店家列表</text>
		</view>
 <uni-list>
 	<uni-list-item clickable="true" showSwitch="true" thumb-size="lg"
	v-for="(item,index) in collectedStoreList"
	:title="item.name" 
	:note="item.sort+' '+item.site" 
	:thumb="item.portrait"
	:switchChecked="item.collect"
	@switchChange="restaurantCollect(index)">
	 </uni-list-item>
</uni-list>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				collectedStoreList:[]
			}
		},
		methods:{
			restaurantClick(index){
					console.log(index+"被点击了");
			},
			restaurantCollect(index){
				console.log(this.collectedStoreList[index].name+"被切换了");
				this.collectedStoreList[index].collect=!this.collectedStoreList[index].collect;
			}
		}
	}	

</script>

<style>
	.restaurantTitle{
		background: #d1d1d1;
		font-weight: 100;
		flex-direction: row;
	}
</style>
