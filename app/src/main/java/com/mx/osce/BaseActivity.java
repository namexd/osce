package com.mx.osce;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.mx.osce.custom.LoadingDialog;
import com.mx.osce.util.Constant;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.Out;
import com.mx.osce.util.RequestManager;
import com.mx.osce.util.Utils;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.Toast;

/**
 * Activity 基础类
 */
public abstract class BaseActivity extends Activity implements OnClickListener {

	protected MyApplicaton mBaseApp;

	private View mDecorView = null;

	public static String TAG;

	public int pageSize = 100;

	public static Context mScontext;

	public static String mSUrl;

	public static String mEasyUrl;

	// private SweetAlertDialog mBaseDialog;

	private LoadingDialog loadingDialog;

	protected void onCreate(Bundle savedInstanceState, Activity activity) {

		super.onCreate(savedInstanceState);

		activity.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
				WindowManager.LayoutParams.FLAG_FULLSCREEN);

		activity.requestWindowFeature(Window.FEATURE_NO_TITLE);

		hideNavigationKey();

		TAG = getClass().getSimpleName();

		mScontext = this;

		settingBasicUrl();

		mBaseApp = (MyApplicaton) this.getApplication();

		mBaseApp.addActivity(this);

		Log.e(mScontext.getClass().getSimpleName(), "onCreate");

	}

	// public void showDialog(Context context, String titleText, String
	// contentText, int type) {
	//
	// mBaseDialog = new SweetAlertDialog(context, type, false);
	// if (titleText != null && titleText.length() > 0) {
	// mBaseDialog.setTitleText(titleText);
	// }
	// if (contentText != null && contentText.length() > 0) {
	// mBaseDialog.setContentText(contentText);
	// }
	// mBaseDialog.setCanceledOnTouchOutside(false);
	// mBaseDialog.setCancelable(false);
	// mBaseDialog.show();
	// }

	public void settingBasicUrl() {

		if (Utils.getSharedPrefrencesByName(mScontext, "BasciUrl", "url") != null) {

			mSUrl = Utils.getSharedPrefrencesByName(mScontext, "BasciUrl", "url");
		} else {

			mSUrl = Constant.BasciUrl;
		}
		mEasyUrl = mSUrl.substring(7, mSUrl.length());
	}

	@SuppressWarnings("unchecked")
	public final <E extends View> E getViewWithClick(int id) {

		try {

			((E) findViewById(id)).setOnClickListener(this);

			return (E) findViewById(id);

		} catch (ClassCastException ex) {

			Out.out("没找到控件");

			throw ex;
		}
	}

	@SuppressWarnings("unchecked")
	public final <E extends View> E getView(int id) {

		try {

			return (E) findViewById(id);

		} catch (ClassCastException ex) {

			Out.out("没找到控件");

			throw ex;
		}
	}

	public void JumpAct(Context ct, Class<?> cls) {

		Intent i = new Intent(ct, cls);

		startActivity(i);
	}

	public void JumpActWithNoData(Class<?> cls) {

		Intent i = new Intent(mScontext, cls);

		startActivity(i);
	}

	public void JumpActAsResult(Context ct, Class<?> cls, int code) {

		Intent i = new Intent(ct, cls);

		i.putExtra("requescode", code);

		startActivityForResult(i, code);
	}

	public void getMyShareperance() {

	}

	/**
	 * return false 执行失败
	 *
	 * @param request
	 */
	public boolean executeRequest(Request<?> request) {
		// new WriteHistory().writeHistory(Utils.getSharedPrefrences(this,
		// "phone"), "url", request.getUrl());
		Log.e(TAG, request.getUrl());

		if (!NetStatus.isNetworkConnected(mScontext)) {
			Out.Toast(BaseActivity.this, "当前没有网络连接！");
			closeProgressDialog();
			return false;
		}
		RequestManager RequestManager = new RequestManager(BaseActivity.this);

		RequestManager.addRequest(request, BaseActivity.this);

		return true;
	}

	/*
	 * private Response.Listener<List<Address>> responseListener() { return new
	 * Response.Listener<List<Address>>() {
	 * 
	 * @Override public void onResponse(final List<Address> response) {
	 * 
	 * } }; }
	 */

	public Response.ErrorListener errorListener() {

		return new Response.ErrorListener() {

			@Override
			public void onErrorResponse(VolleyError error) {

				closeProgressDialog();

				Out.Toast(BaseActivity.this, "网络请求数据异常！");

				error.printStackTrace();

				Log.e(">>>VolleyError<<<", error.getMessage() + "");
			}

		};
	}

	public void openProgressDialog() {
		if (loadingDialog == null) {
			loadingDialog = new LoadingDialog(this);
		}
		if (!loadingDialog.isShowing())
			loadingDialog.show();
	}

	public void closeProgressDialog() {
		if (loadingDialog != null) {
			loadingDialog.cancel();
		}
	}

	public Response.ErrorListener errorListenerCancelDialog() {

		return new Response.ErrorListener() {

			@Override
			public void onErrorResponse(VolleyError error) {
				closeProgressDialog();

				Out.Toast(BaseActivity.this, "取消网络异常！");

				error.printStackTrace();
			}
		};

	}

	public void Out(String str) {
		Out.out(str);
	}

	public void Toast(String str) {
		Out.Toast(this, str);
	}

	@Override
	protected void onSaveInstanceState(Bundle outState) {
		super.onSaveInstanceState(outState);

	}

	public void CustomToast(String str) {
		Toast ToastTemp = Toast.makeText(this, str, Toast.LENGTH_SHORT);
		ToastTemp.setGravity(Gravity.CENTER, 0, 0);
		ToastTemp.show();
	}

	@Override
	protected void onResume() {
		super.onResume();

	}

	@Override
	protected void onPause() {
		super.onPause();
	}

	@Override
	public void onWindowFocusChanged(boolean hasFocus) {
		super.onWindowFocusChanged(hasFocus);

		if (hasWindowFocus()) {
			hideNavigationKey();
		}
	}

	@Override
	public boolean dispatchTouchEvent(MotionEvent ev) {
		if (ev.getAction() == MotionEvent.ACTION_DOWN) {
			View v = getCurrentFocus();
			if (isShouldHideInput(v, ev)) {
				InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
				if (imm != null) {
					imm.hideSoftInputFromWindow(v.getWindowToken(), 0);
					hideNavigationKey();
				}
			}
			return super.dispatchTouchEvent(ev);
		}
		// Necessary Part, or All other components will not have TouchEvent.
		if (getWindow().superDispatchTouchEvent(ev)) {
			return true;
		}
		return super.dispatchTouchEvent(ev);
	}

	public boolean isShouldHideInput(View v, MotionEvent event) {
		if (v != null && (v instanceof EditText)) {
			int[] leftTop = { 0, 0 };
			// 获取输入框当前的location位置
			v.getLocationInWindow(leftTop);
			int left = leftTop[0];
			int top = leftTop[1];
			int bottom = top + v.getHeight();
			int right = left + v.getWidth();
			if (event.getX() > left && event.getX() < right && event.getY() > top && event.getY() < bottom) {
				// 点击的是输入框区域，保留点击EditText的事件
				return false;
			} else {
				return true;
			}
		}
		return false;
	}

	private void hideNavigationKey() {
		mDecorView = getWindow().getDecorView();
		mDecorView.setSystemUiVisibility(View.SYSTEM_UI_FLAG_LAYOUT_STABLE | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
				| View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION // hide
																								// nav
																								// bar
				| View.SYSTEM_UI_FLAG_FULLSCREEN // hide status bar
				| View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY);
	}
}