package com.mx.test.custom;

import android.content.Context;
import android.graphics.Typeface;

public class FontResource {
	static FontResource instance;
	static Typeface mSty;
	// Style
	public static final int NORMAL = 0;
	public static final int BOLD = 1;
	public static final int ITALIC = 2;
	public static final int BOLD_ITALIC = 3;

	/**
	 * 得到指定文件的字体资源文件的单例
	 * 
	 * @param ctx
	 *            上下文
	 * @param assets
	 *            Assets文件夹下的文件名
	 * @return 字体资源文件的单例
	 */
	public static FontResource getInstrance(Context ctx, String assets) {
		synchronized (FontResource.class) {
			if (instance == null) {
				instance = new FontResource();
				mSty = Typeface.createFromAsset(ctx.getResources().getAssets(), assets);
			}
		}
		return instance;
	}

	/** 得到默认正常的ttf字体 */
	public Typeface getTypeface() {

		return mSty;
	}

	/**
	 * 得到指定风格 ttf字体样式
	 * 
	 * @param style
	 *            可设置：0,1,2,3
	 * 
	 *            <p>
	 *            正常0，粗体1，斜体2，粗斜3
	 *            </p>
	 * 
	 * @return 指定Style的Typeface
	 */
	public Typeface getTypeface(int style) {

		return Typeface.create(mSty, style);
	}
}
