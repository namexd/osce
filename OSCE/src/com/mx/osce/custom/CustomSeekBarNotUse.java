package com.mx.osce.custom;

import android.content.Context;
import android.util.AttributeSet;
import android.view.Gravity;
import android.view.View;
import android.widget.FrameLayout;
import android.widget.LinearLayout;
import android.widget.SeekBar;
import android.widget.SeekBar.OnSeekBarChangeListener;
import android.widget.TextView;

public class CustomSeekBarNotUse extends LinearLayout {
	// 显示当前进度
	private TextView mCurrentPositionTv;
	// 进度条
	public SeekBar mSeekBar;
	// 刻度的layout,当前刻度的layout
	private FrameLayout tickLayout, scorePaneLayout;
	// 当进度改变时候触发的接口
	private OnMySeekBarChangeListener onMySeekBarChangeListener;
	//
	private int spacingDistance = 1;
	// 默认刻度为10
	private int max = 10;
	// 整数样式
	private String format = "%d";

	/** 最小进度值与最大进度值 */
	private TextView mTextHeadOrEnd;

	public interface OnMySeekBarChangeListener {
		public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser);
	}

	public void setOnMySeekBarChangeListener(CustomSeekBarNotUse.OnMySeekBarChangeListener listner) {
		onMySeekBarChangeListener = listner;
	}

	public CustomSeekBarNotUse(Context context, AttributeSet attrs) {
		super(context, attrs);
	}

	public CustomSeekBarNotUse(Context context) {
		super(context);
	}

	private void init(Context context) {

		setOrientation(LinearLayout.VERTICAL);
		scorePaneLayout = new FrameLayout(context);
		scorePaneLayout.setLayoutParams(new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT));

		// scorePaneLayout.setPadding(20, 0, 20, 0);
		for (int i = 0; i < 2; i++) {
			mTextHeadOrEnd = new TextView(context);
			LayoutParams paramsTv = new LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT);
			if (i % 2 == 0) {
				mTextHeadOrEnd.setText(String.format(format, i));
			} else {
				mTextHeadOrEnd.setText(String.format(format, max));
			}
			mTextHeadOrEnd.setLayoutParams(paramsTv);
			// mTextHeadOrEnd.setBackgroundResource(R.drawable.gary);
			mTextHeadOrEnd.setGravity(Gravity.CENTER);
			mTextHeadOrEnd.setVisibility(View.VISIBLE);
			scorePaneLayout.addView(mTextHeadOrEnd);
		}
		// 当前刻度值
		mCurrentPositionTv = new TextView(context);
		mCurrentPositionTv.setLayoutParams(new LayoutParams(LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT));
		mCurrentPositionTv.setText(String.format(format, 0));
		// mCurrentPositionTv.setBackgroundColor(R.drawable.orange);
		scorePaneLayout.addView(mCurrentPositionTv);
		addView(scorePaneLayout);

		mSeekBar = new SeekBar(context);

		mSeekBar.setLayoutParams(new LayoutParams(-1, -2));
		// mSeekBar.setPadding(20, 0, 20, 0);
		mSeekBar.setMax(max);
		mSeekBar.setOnSeekBarChangeListener(new OnSeekBarChangeListener() {

			@Override
			public void onStopTrackingTouch(SeekBar seekBar) {

			}

			@Override
			public void onStartTrackingTouch(SeekBar seekBar) {

			}

			@Override
			public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
				progressChangeHandle(seekBar, progress, fromUser);
			}
		});
		addView(mSeekBar);
		// 动态添加刻度
		tickLayout = new FrameLayout(context);
		tickLayout.setLayoutParams(new LayoutParams(-1, -2));
		for (int i = 0; i <= max; i++) {
			if (i % spacingDistance == 0) {
				TextView tickTv = new TextView(context);
				tickTv.setText(String.valueOf(i));
				FrameLayout.LayoutParams paramsTv = new FrameLayout.LayoutParams(-2, -2);
				tickTv.setLayoutParams(paramsTv);
				tickLayout.addView(tickTv);
			}

		}
		addView(tickLayout);
	}

	/***
	 * 改变控件上部的刻度指向
	 * 
	 * @param seekBar
	 * @param progress
	 * @param fromUser
	 */
	protected void progressChangeHandle(SeekBar seekBar, int progress, boolean fromUser) {
		mCurrentPositionTv.setText(String.format(format, progress));

		FrameLayout.LayoutParams params = (FrameLayout.LayoutParams) mCurrentPositionTv.getLayoutParams();
		mCurrentPositionTv.setLayoutParams(params);
		if (progress == max || progress == 0) {

			mCurrentPositionTv.setVisibility(View.GONE);

		} else {
			mCurrentPositionTv.setVisibility(View.VISIBLE);
		}
		int maxMargin = seekBar.getMeasuredWidth() - mCurrentPositionTv.getMeasuredWidth();
		int leftMargin = seekBar.getMeasuredWidth() * progress / max - mCurrentPositionTv.getMeasuredWidth() / 2;

		leftMargin = leftMargin >= 0 ? leftMargin : 0;

		leftMargin = leftMargin <= maxMargin ? leftMargin : maxMargin;

		params.leftMargin = leftMargin;

		if (onMySeekBarChangeListener != null) {
			onMySeekBarChangeListener.onProgressChanged(seekBar, progress, fromUser);
		}
	}

	@Override
	protected void onFinishInflate() {
		super.onFinishInflate();
		init(getContext());
	}

	@Override
	protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {

		if (mSeekBar != null) {

			// widthMeasureSpec = mSeekBar.getWidth();
			// heightMeasureSpec = mSeekBar.getHeight();
			measureChild(mSeekBar, widthMeasureSpec, heightMeasureSpec);
		}

		View scorePaneRight = scorePaneLayout.getChildAt(1);
		measureChild(scorePaneRight, widthMeasureSpec, heightMeasureSpec);
		FrameLayout.LayoutParams params = (FrameLayout.LayoutParams) scorePaneRight.getLayoutParams();
		params.leftMargin = mSeekBar.getMeasuredWidth() - scorePaneRight.getMeasuredWidth();
		scorePaneRight.setLayoutParams(params);

		for (int i = 0; i < tickLayout.getChildCount(); i++) {
			View view = tickLayout.getChildAt(i);
			measureChild(view, widthMeasureSpec + getPaddingLeft() + getPaddingRight(), heightMeasureSpec);
			params = (FrameLayout.LayoutParams) view.getLayoutParams();
			if (i == tickLayout.getChildCount() - 1) {
				params.leftMargin = i * mSeekBar.getMeasuredWidth() / max - view.getMeasuredWidth();
			} else if (i != 0) {
				params.leftMargin = i * mSeekBar.getMeasuredWidth() / max - view.getMeasuredWidth() / 2;
			}
			view.setLayoutParams(params);
		}
		super.onMeasure(widthMeasureSpec, heightMeasureSpec);
	}

	public void setSeekBarMax(int max) {
		this.max = max;
		removeAllViews();
		init(getContext());
	}

	public void setFormat(String format) {
		if (this.format.equals(format)) {
			return;
		}
		this.format = format;
		if (mCurrentPositionTv != null) {
			mCurrentPositionTv.setText(String.format(format, mSeekBar.getProgress()));
		}
	}

	public TextView getCurrentPositionTextView() {
		return mCurrentPositionTv;
	}

	/** 设置当前进度的背景 */
	public void setCurrentProgressDrawabel(int resId) {
		mCurrentPositionTv.setBackgroundResource(resId);
	}

	/** 设置最大最小值的背景 */
	public void setHeadOrEndDrawable(int resId) {
		mTextHeadOrEnd.setBackgroundResource(resId);
	}

	public SeekBar getSeekBar() {
		return mSeekBar;
	}

	public void setScorePaneVisible(boolean isShow) {

		for (int i = 0; i < scorePaneLayout.getChildCount() - 1; i++) {
			View view = scorePaneLayout.getChildAt(i);
			if (isShow) {
				view.setVisibility(View.VISIBLE);
			} else {
				view.setVisibility(View.GONE);
			}
		}
	}

}
