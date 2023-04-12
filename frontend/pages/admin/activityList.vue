<template>
	<view class="activityList">
		<view v-if="listNum">
				<uni-section title="活动列表" type="line">
					<uni-list v-for="(item, index) in acts">
						<uni-swipe-action>
							<uni-swipe-action-item :right-options="options" @click="bindClick($event, index)">
								<uni-list-item thumbSize="lg"
								:title="item.activityName"
								:thumb="item.picture"
								:note="item.slogan">
								</uni-list-item>
							</uni-swipe-action-item>
						</uni-swipe-action>
					</uni-list>
				</uni-section>
		</view>
		<uni-load-more v-else status="noMore" :contentText="contenDown"></uni-load-more>
		<!-- <text v-else class="text">没有未处理的申诉</text> -->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				listNum: "",
				acts: [],
				contenDown: {
					contentnomore: '没有需要处理的活动'
				},
				options:[{
				    text: '取消',
				    style: {
				        backgroundColor: '#007aff'
				    }
				}, {
					text: '删除',
					style: {
				        backgroundColor: '#dd524d'
					}
				}]
			}
		},
		onShow() {
			this.getActList()
		},
		onLoad() {
			this.getActList()
		},
		methods: {
			getActList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getAllActivities',
					method: 'GET',
					success: (res) => {
						console.log(res)
						let actList = res.data.data.activityList
						this.acts = actList
						this.listNum = actList.length
						// this.listNum = 0  // for test
					}
				})
			},
			bindClick(e, index) {
				let that = this
				let actId = this.acts[index].activityId
				if(e.index) {
					uni.showModal({
						title: '提示',
						content: '是否要删除该活动',
						success: function (res) {
							if (res.confirm) {
								uni.request({
									url: 'http://124.71.170.100/index.php/index/deleteActivity',
									data: {
										"activityId": actId
									},
									method: 'GET',
									success: (res) => {
										that.getActList()
									}
								})
							}
						}
					})
				}
			}
		}
	}
</script>

<style>
	.text{
		margin: auto 200rpx;
	}
</style>
