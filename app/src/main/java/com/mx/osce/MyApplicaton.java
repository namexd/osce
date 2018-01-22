package com.mx.osce;

import java.io.File;
import java.util.Stack;

import com.mx.osce.crashHandler.CrashHandler;
import com.mx.osce.save.FileUtils;
import com.mx.osce.service.SubscriberService;

import android.app.Activity;
import android.app.Application;
import android.content.Intent;
import android.os.Environment;
import android.util.Log;

public class MyApplicaton extends Application {

	private static String TAG = MyApplicaton.class.getName();

	/* Activity栈 */
	private static Stack<Activity> mSactivityStack = new Stack<Activity>();

	static Intent mSubscriberIntent;// 服务Intent

	@Override
	public void onCreate() {
		super.onCreate();

		mSubscriberIntent = new Intent(getApplicationContext(), SubscriberService.class);
		
//		CrashHandler crashHandler = CrashHandler.getInstance();
//		crashHandler.init(getApplicationContext());
	}

	public void stopservice() {
		Log.e(TAG, "SubscriberService" + "******stop");
		stopService(mSubscriberIntent);
	}

	public void startService() {
		startService(mSubscriberIntent);
	}

	public static Intent getmSubscriberIntent() {
		if (mSubscriberIntent == null) {
			return null;
		}
		return mSubscriberIntent;
	}

	public static void setmSubscriberIntent(Intent mSubscriberIntent) {
		MyApplicaton.mSubscriberIntent = mSubscriberIntent;
	}

	public void addActivity(final Activity currentActivity) {
		if (null == mSactivityStack) {
			mSactivityStack = new Stack<Activity>();
		}
		mSactivityStack.add(currentActivity);
	}

	public void removeActivity(final Activity currentActivity) {
		if (null == mSactivityStack) {
			mSactivityStack = new Stack<Activity>();
		}
		mSactivityStack.remove(currentActivity);
	}

	// 获取最后一个Activity
	public Activity currentActivity() {
		Activity activity = mSactivityStack.lastElement();
		return activity;
	}

	// 返回栈内Activity的总数
	public int NumberOfActivity() {
		return mSactivityStack.size();
	}

	// 关闭所有Activity
	public void finishAllActivities() {
		for (int i = 0, size = mSactivityStack.size(); i < size; i++) {
			if (null != mSactivityStack.get(i)) {
				mSactivityStack.get(i).finish();
			}
		}
		mSactivityStack.clear();
	}

	/** 退出App */
	public void exit() {

		FileUtils
				.deleteDirectory(Environment.getExternalStorageDirectory().toString() + File.separator + "com.mx.osce");
		stopService(mSubscriberIntent);// 离开时停止服务
		finishAllActivities();// 结束所有Activity
		android.os.Process.killProcess(android.os.Process.myPid());
		System.exit(0);
	}
}