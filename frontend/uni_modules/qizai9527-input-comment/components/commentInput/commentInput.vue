<template>
	<view :style="'display: '+showDisplay">
		<view  @click="clickOther" class="qizai9527-comment-other"></view>
		<view class="qizai9527-comment">
			<view class="qizai9527-comment-info">
				<textarea 
					v-model="value" 
					:placeholder="placeholder" 
					:focus="isShow"
					auto-height="true" />
				<view @click.stop="submitComment" class="qizai9527-comment-submit" :style="'color:'+submitColor">发布</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		props: {
			placeholder:{
				type:String,
				default:'发个友善的评论吧'
			},
			isShow:{
				type:Boolean,
				default:false
			},
			valueData:{
				type:Array,
				default:[]
			},
			curKey:{
				type:String,
				default:''
			}
		},
	    components: {},
		data() {
			return {
				screenHeight:0,
				submitColor:"#b0b0b0",
				value:'',
				showDisplay:'none',
			}
		},
		methods: {
			inputValue(e){
				if(this.value){
					this.submitColor = '#fb5f5f';
				}else{
					this.submitColor = "#b0b0b0";
				}
			},
			submitComment(){
				if(!this.value) return;
				this.$emit('submitComment',this.value);
			},
			clickOther(){
				this.$emit('clickOther',this.value);
			}
		},
		watch:{
			value:function(val,oldVal){
				this.inputValue(val);
			},
			isShow:function(val,oldVal){
				this.value = this.valueData.hasOwnProperty(this.curKey) ? this.valueData[this.curKey]:'';
				this.showDisplay = val?'block':'none';
			},
			valueData:function(val,oldVal){
				// console.log(val,oldVal);
			}
		}
	}
</script>
<style>
	.qizai9527-comment-other{
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.qizai9527-comment{
		font-size: 30upx;
		z-index: 999;
		position: fixed;
		bottom: 0;
		z-index: 100;
		width: 100%;
		background-color: white;
		padding: 20upx;
		border-top: 1px solid #d4d4d494;
	}
	.qizai9527-comment-info{
		position: relative;
	}
	.qizai9527-comment-info textarea{
		width: 80%;
		display: inline-block;
		background-color:rgba(232, 232, 232, 0.57);
		padding: 10upx;
	}
	.qizai9527-comment-submit{
		position: absolute;
		bottom: 0;
		width: 15%;
		display: inline-block;
		color: #b0b0b0;
		margin-left: 2%;
		font-weight: bold;
		padding-bottom: 20upx;
	}
</style>
