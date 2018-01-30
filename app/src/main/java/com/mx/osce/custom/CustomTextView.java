package com.mx.osce.custom;

import com.mx.osce.R;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Typeface;
import android.util.AttributeSet;
import android.widget.TextView;

public class CustomTextView extends TextView {

	private String customFont;
	static final String TAG = "CustomTextView";

	public CustomTextView(Context context) {
		super(context);
	}

	public CustomTextView(Context context, AttributeSet attrs) {
		super(context, attrs);
		setCustomFont(context, attrs);
	}

	public CustomTextView(Context context, AttributeSet attrs, int defStyleAttr) {
		super(context, attrs, defStyleAttr);
		setCustomFont(context, attrs);
	}

	// 拿到ttf文件名
	private void setCustomFont(Context context, AttributeSet attrs) {
		TypedArray a = context.obtainStyledAttributes(attrs, R.styleable.CustomTextView);
		customFont = a.getString(R.styleable.CustomTextView_customFont);
		if (null == customFont) {
			a.recycle();
			return;
		} else {
			setCustomFont(context, customFont);
			a.recycle();
		}
	}

	/**
	 * 拿到ttf的文件名字，来设置不同的字体
	 * 
	 * @param ctx
	 *            上下文
	 * @param customFont
	 *            ttf文件名
	 * @return 返回设置字体是否成功
	 */
	public void setCustomFont(Context context, String customFont) {

		Typeface tf = FontResource.getInstrance(context, customFont).getTypeface();

		this.setTypeface(tf);
	}

}
