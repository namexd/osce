package com.mx.osce.fragment;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.mx.osce.BaseActivity;
import com.mx.osce.material.CircleProgressBar;
import com.mx.osce.util.NetStatus;
import com.mx.osce.util.Out;
import com.mx.osce.util.RequestManager;

import android.app.Activity;
import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.MotionEvent;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;

public class BaseFragment extends Fragment {
	public static String TAG;
	public int pageSize = 100;
	public static Context mScontext;
	private CircleProgressBar loadingBar;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
		mScontext = getActivity();
		TAG = getClass().getSimpleName();
		return super.onCreateView(inflater, container, savedInstanceState);

	}

	public void closeProgressDialog() {

		if (loadingBar == null) {
			return;
		} else {
			loadingBar.setVisibility(View.GONE);
		}
	}

	public void openProgressDialog() {
		if (loadingBar == null) {
			loadingBar = new CircleProgressBar(mScontext);
			loadingBar.setShowArrow(true);
			loadingBar.setVisibility(View.VISIBLE);
		}
	}

	/**
	 * return false 执行失败
	 *
	 * @param request
	 */
	public boolean executeRequest(Request<?> request) {
		Log.i(TAG, request.getUrl());
		if (!NetStatus.isNetworkConnected(mScontext)) {
			Out.Toast(mScontext, "当前没有网络连接！");
			return false;
		}
		RequestManager rmg = new RequestManager(mScontext);
		RequestManager.addRequest(request, mScontext);
		return true;
	}

	public Response.ErrorListener errorListener() {
		return new Response.ErrorListener() {
			@Override
			public void onErrorResponse(VolleyError error) {
				Out.Toast(mScontext, "网络连接异常!");
				error.printStackTrace();
			}

		};
	}

	public Response.ErrorListener errorListenerCancelDialog() {
		return new Response.ErrorListener() {

			@Override
			public void onErrorResponse(VolleyError error) {
				Out.Toast(mScontext, "取消网络异常！");
				error.printStackTrace();
			}
		};

	}

	public void Out(String str) {
		Out.out(str);
	}

	public void Toast(String str) {
		Out.Toast(mScontext, str);
	}

}
