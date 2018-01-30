package com.mx.bluetooth.broadcast;

import com.google.gson.Gson;
import com.mx.bluetooth.bean.BaseInfo;
import com.mx.bluetooth.util.Constant;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;

public class SubscriberReciver extends BroadcastReceiver {

	public static final String ACTION_SUBSCRIBE = "com.mx.bluetooth.broadcast.SubscriberReciver";

	@Override
	public void onReceive(final Context context, Intent intent) {

		if (intent.getAction().equals(ACTION_SUBSCRIBE)) {
			String jsonStr = intent.getStringExtra("Message").trim();
			Log.e("Subscriber", "广播接受的消息" + jsonStr);
			BaseInfo inforBean = null;// 基本json
			int subTypeCode = -1;// 订阅码类型
			if (jsonStr != null && jsonStr.length() > 0) {
				inforBean = new Gson().fromJson(jsonStr, BaseInfo.class);
				subTypeCode = inforBean.getCode();
			}
			switch (subTypeCode) {// 判断接受的订阅码

			case Constant.CODE_OFF_LINE:// 下线100

				Intent offIntent = new Intent(context, ForceBroadReciver.class);
				offIntent.putExtra("Message", jsonStr);
				offIntent.setAction(Constant.ACTION_FORCE_OFFINE);
				context.sendBroadcast(offIntent);

				break;

			case Constant.CODE_CURRENT_STUDENT:// 当前考生102

				Intent currentStudentIntent = new Intent();
				currentStudentIntent.putExtra("Message", jsonStr);
				currentStudentIntent.setAction(Constant.ACTION_CHANGE_CURRENT_STUDENT);
				context.sendBroadcast(currentStudentIntent);

				break;

			case Constant.CODE_CURRENT_GROUP:// 当前小组103

				Intent currentGroupIntent = new Intent();
				currentGroupIntent.putExtra("Message", jsonStr);
				currentGroupIntent.setAction(Constant.ACTION_CHANGE_CURRENT_GROUP);
				context.sendBroadcast(currentGroupIntent);
				break;

			case Constant.CODE_NEXT_GROUP:// 下一组104

				Intent nextGroupIntent = new Intent();
				nextGroupIntent.putExtra("Message", jsonStr);
				nextGroupIntent.setAction(Constant.ACTION_CHANGE_NEXT_GROUP);
				context.sendBroadcast(nextGroupIntent);

				break;

			case Constant.CODE_BEGIN_EXAM:// PC 开始理论考试105

				Intent beginIntent = new Intent();
				beginIntent.putExtra("Message", jsonStr);
				beginIntent.setAction(Constant.ACTION_THEORY_EXAM_BEGIN);
				context.sendBroadcast(beginIntent);

				break;

			case Constant.CODE_WARN_END_EXAM:// 警告后确认结束考试106

				Intent endIntent = new Intent(context, ForceBroadReciver.class);
				endIntent.putExtra("Message", jsonStr);
				endIntent.setAction(Constant.ACTION_WARN_EXAM_END);
				context.sendBroadcast(endIntent);

				break;

			case Constant.CODE_GIVE_UP_EXAM:// 放弃考试107

				Intent giveUpIntent = new Intent();
				giveUpIntent.putExtra("Message", jsonStr);
				giveUpIntent.setAction(Constant.ACTION_GIVE_UP_EXAM);
				context.sendBroadcast(giveUpIntent);

				break;

			case Constant.CODE_THRORY_EXAM_BY_PC_END:// PC理论考试结束108

				Intent endTheoryExam = new Intent();
				endTheoryExam.putExtra("Message", jsonStr);
				endTheoryExam.setAction(Constant.ACTION_THEORY_EXAM_END);
				context.sendBroadcast(endTheoryExam);

				break;
			default:
				break;

			}
		}
	}

}
