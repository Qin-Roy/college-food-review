# 评论输入框

## 说明

组件功能
1.触发弹出输入框  
2.点击空白隐藏返回
3.输入的内容带记忆功能，对于不同的评论体会可保存已输入的评论内容，当下次再触发时，可找到上次输入的内容。
4.点击发布触发事件返回输入的内容

## 基本用法

在template中使用组件  
```html
	<qizai9527-input-comment 
		:placeholder="placeholder" 
		:valueData="tmpCommentData" 
		:isShow="isShowComment" 
		:curKey="curCommentId"
		@submitComment="submitComment"  
		@clickOther="clickOther">
	</qizai9527-input-comment>
```  

```javascript
	export default {
		data() {
			return {
				nameList:{
					1:'小心',
					2:'大呃呃',
					3:'小气鬼',
				},
				
				isShowComment:false,
				placeholder:"友善是交流的起点",
				tmpCommentData:[],
				curCommentId:'',
			}
		},
		methods: {
			// 点击评论触发评论输入框
			clickComment(id){
				this.curCommentId = id;
				this.placeholder = "回复 "+ this.nameList[id] + "：";
				this.isShowComment=true;
			},
			// 提交评论并清除记忆
			submitComment(e){
				this.isShowComment=false;
				this.tmpCommentData[this.curCommentId] = '';
			},
			// 点击空白返回并保存记忆
			clickOther(e){
				this.isShowComment=false;
				this.tmpCommentData[this.curCommentId] = e;
			}
		}
	}
```

## API

**属性说明**

|属性名|类型|默认值|说明|
:---:|:----:|:---:|:--:|
|placeholder|String|null|提示信息|
| isShowComment | Boolean |  false  | 是否显示评论输入框，根据这个字段控制显示或隐藏 |
| avatar | Number |  null  | 用户头像 |
| curKey | Boolean |  null  | 当前组件记忆数据的key值，根据此值查找对于的记忆值 |
| valueData | String |  null  | 记忆数据，与curKey配对使用，可对此值赋值，来控制输入框curKey对应的值 |

**事件说明**  

|   事件名   |  说明  |返回值 |
| :--- : | :--: | :----: |
|  @submitComment   | 点击发布评论触发 | 返回评论内容 |
| @clickOther | 点击空白区域触发|  返回评论内容    |


补充：有任何问题联系wx：chwlzgz 。在线求打扰~