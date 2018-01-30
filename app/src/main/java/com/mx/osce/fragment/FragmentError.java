package com.mx.osce.fragment;

import com.mx.osce.R;

import android.app.Activity;
import android.app.Fragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.Button;

public class FragmentError extends Fragment implements OnClickListener {

	onRefrashListener mListener;

	public interface onRefrashListener {
		void onRefrash();
	}

	Activity mContext;

	private Button mBtnRefrash;// 刷新

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view = inflater.inflate(R.layout.error_fragment, null);
		mContext = getActivity();
		initWidget(view);
		return view;
	}

	// 初始化 控件
	private void initWidget(View view) {
		mBtnRefrash = (Button) view.findViewById(R.id.mBtnRefrash);
		mBtnRefrash.setOnClickListener(this);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {

		case R.id.mBtnRefrash:
			mListener.onRefrash();
			break;
		}
	}

	@Override
	public void onAttach(Activity activity) {
		super.onAttach(activity);
		try {
			mListener = (onRefrashListener) activity;
		} catch (ClassCastException e) {
			throw new ClassCastException(activity.toString() + "must implement Show_PreViewInf_uponFragmentEvaluate");
		}
	}

}
