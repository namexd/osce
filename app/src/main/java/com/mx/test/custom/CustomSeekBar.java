package com.mx.test.custom;

import com.mx.test.R;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.RectF;
import android.text.TextUtils;
import android.util.AttributeSet;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;

/**
 * Created by chj on 2016/5/11.
 */
public class CustomSeekBar extends View {
	private static final String TAG = CustomSeekBar.class.getSimpleName();
	private static final int DEFAULT_MIN = 0;
	private static final int DEFAULT_MAX = 80;
	private static final int DEFAULT_ITEMS_NUM = 9;
	private static final int DEFAULT_POPUP_TEXT_SIZE = 15;
	private static final int DEFAULT_MARK_TEXT_SIZE = 15;
	private static final int DEFAULT_PROGRESS_HEIGHT = 15;
	private static final int DEFAULT_MARK_TEXT_COLOR = 0xFF0000FF;
	private static final String[] DEFAULT_ITEMS_ARRAY = new String[] { "0", "1", "2", "3", "4", "5", "6", "7", "8" };

	/* 背景颜色 */
	private int mBackGroundColor = Color.GRAY;
	/* 进度条颜色 */
	private int mProgressColor = Color.BLUE;
	/* Popup字体的颜色 */
	private int mPopupTextColor = Color.WHITE;
	/* Popup字体的大小 */
	private int mPopupTextSize = DEFAULT_POPUP_TEXT_SIZE;
	private int mMarkTextSize = DEFAULT_MARK_TEXT_SIZE;
	private int mMarkItemsNum = DEFAULT_ITEMS_NUM;
	// 刻度的下标名称数组
	private String[] mMarkDescArray = DEFAULT_ITEMS_ARRAY;
	/* 真实的进度 */
	private int mLastPressedX;
	private int mLastPressedY;
	private int mPressedProgress;
	private int mCurProgress = 0;
	private int mMin = DEFAULT_MIN;
	private int mMax = DEFAULT_MAX;
	private int mSpacing = (mMax - mMin) / (mMarkItemsNum - 1);
	/* 最后停留的位置 */
	private int mLastMotionX;
	/* X轴起始位置 */
	private int mOffSet_X;
	/* 进度条高度 */
	private int progressHeight;
	/* 进度条高度边距 */
	private int progressMargin;
	/* 上方标示图片 */
	private Bitmap markBitMap = null;
	/* 上方图片宽 */
	private int markBitMapWidth;
	/* 上方图片高 */
	private int markBitMapHeight;
	/* thum与progressBar的距离 */
	private int progressToThumbMargin;
	/* 起始字体颜色 */
	private int mMarkTextColor[];
	/* 上方显示的进度文字 */
	private String mPopupString = null;

	private boolean mIsShowPopup = true;

	// 监听回调接口
	private OnSeekBarChangeListener mOnSeekBarChangeListener;

	public interface OnSeekBarChangeListener {

		public void OnProgressChanged(int id, int min, int max, int space, int progress);

		public void OnProgressUpdated(int id, int min, int max, int space, int progress);

		public String OnPopupTextChanged(int id, int min, int max, int space, int progress);
	}

	public CustomSeekBar(Context context) {
		super(context);
		initPara(context, null);
	}

	public CustomSeekBar(Context context, AttributeSet attrs) {
		super(context, attrs);
		initPara(context, attrs);
	}

	public CustomSeekBar(Context context, AttributeSet attrs, int defStyleAttr) {
		super(context, attrs, defStyleAttr);
		initPara(context, attrs);
	}

	public void showPopup() {
		if (!mIsShowPopup) {
			mIsShowPopup = true;
			postInvalidate();
		}
	}

	public void hidePopup() {
		mIsShowPopup = false;
		postInvalidate();
	}

	private void initPara(Context context, AttributeSet attrs) {
		mMarkTextColor = new int[mMarkItemsNum];
		for (int i = 0; i < mMarkItemsNum; i++) {
			mMarkTextColor[i] = Color.BLACK;
		}

		TypedArray typedArray = context.obtainStyledAttributes(attrs, R.styleable.CustomSeekBar);
		final int N = typedArray.getIndexCount();
		for (int i = 0; i < N; i++) {
			int attr = typedArray.getIndex(i);
			//
			if (attr == R.styleable.CustomSeekBar_markItemNum) {
				this.mMarkItemsNum = typedArray.getInteger(attr, DEFAULT_ITEMS_NUM);
			} else if (attr == R.styleable.CustomSeekBar_max) {
				this.mMax = typedArray.getInteger(attr, DEFAULT_MAX);
			} else if (attr == R.styleable.CustomSeekBar_min) {
				this.mMin = typedArray.getInteger(attr, DEFAULT_MIN);
			} else if (attr == R.styleable.CustomSeekBar_markDescArray) {
				String content = typedArray.getString(attr);
				if (!TextUtils.isEmpty(content)) {
					String[] strings = content.split("\\|");// 以|作为分隔符.
					this.mMarkDescArray = strings;
				}
			} else if (attr == R.styleable.CustomSeekBar_markTextColor) {
				for (int j = 0; j < mMarkTextColor.length; j++) {
					mMarkTextColor[j] = typedArray.getInteger(attr, DEFAULT_MARK_TEXT_COLOR);
				}
			} else if (attr == R.styleable.CustomSeekBar_backgroundColor) {
				mBackGroundColor = typedArray.getInteger(attr, Color.GRAY);
			} else if (attr == R.styleable.CustomSeekBar_selectedColor) {
				mProgressColor = typedArray.getInteger(attr, Color.BLUE);
			} else if (attr == R.styleable.CustomSeekBar_progressHeight) {
				progressHeight = dip2px(getContext(), typedArray.getDimensionPixelSize(attr, DEFAULT_PROGRESS_HEIGHT));
			} else if (attr == R.styleable.CustomSeekBar_isShowPopup) {
				mIsShowPopup = typedArray.getBoolean(attr, true);
			}
		}
		typedArray.recycle();

		this.mSpacing = (mMax - mMin) / (mMarkItemsNum - 1);
		mOffSet_X = 10;
		mPopupTextSize = dip2px(getContext(), DEFAULT_POPUP_TEXT_SIZE);
		mMarkTextSize = dip2px(getContext(), DEFAULT_MARK_TEXT_SIZE);
		progressToThumbMargin = dip2px(getContext(), 5);
		markBitMapWidth = dip2px(getContext(), 23);
		markBitMapHeight = dip2px(getContext(), 28);
		markBitMap = BitmapFactory.decodeResource(getResources(), R.drawable.popup_bg_pic);

		Log.i(TAG, "Init parameter finished...");
	}

	private static int dip2px(Context context, float dipValue) {
		final float scale = context.getResources().getDisplayMetrics().density;
		return (int) (dipValue * scale + 0.5f);
	}

	private int getProgressHeight() {
		if (progressHeight > 0)
			return progressHeight;
		return dip2px(getContext(), DEFAULT_PROGRESS_HEIGHT);
	}

	private int getProgressMargin() {
		if (progressMargin > 0)
			return progressMargin;
		return dip2px(getContext(), 25);
	}

	private int getProgressLength() {
		return getWidth() - getProgressMargin() * 2;
	}

	@Override
	protected void onDraw(Canvas canvas) {
		super.onDraw(canvas);

		// 根据progress得到滑动的位置
		int bgW = getWidth() - getProgressMargin() * 2;
		mLastMotionX = bgW * mCurProgress / mMax + getProgressMargin();

		if (mLastMotionX <= getProgressMargin())
			mLastMotionX = getProgressMargin() + mOffSet_X;

		drawProgressBg(canvas);
		drawProgressBar(canvas);
		if (mIsShowPopup) {
			drawBitMap(canvas);
			drawPopupText(canvas);
		}

		drawMarkItemText(canvas);
	}

	public void setViewPara(int markNo, String[] markItems, int min, int max) {
		mMarkItemsNum = markNo;
		mMarkDescArray = markItems;
		mMax = max;
		mMin = min;
		mSpacing = (mMax - mMin) / (mMarkItemsNum - 1);

		mMarkTextColor = new int[markNo];
		for (int i = 0; i < markNo; i++) {
			mMarkTextColor[i] = Color.BLACK;
		}
		int potint = 0;
		if (mCurProgress % mSpacing > mSpacing / 2) {
			potint = mCurProgress / mSpacing + 1;
		} else {
			potint = mCurProgress / mSpacing;
		}
		mPopupString = String.valueOf(potint);
	}

	/**
	 * 重新绘制控件.
	 */
	public void reDraw() {
		postInvalidate();
	}

	private void drawProgressBg(Canvas canvas) {
		// 画圆角矩形
		Paint p = new Paint();
		p.setStyle(Paint.Style.FILL);// 充满
		p.setColor(mBackGroundColor);
		p.setAntiAlias(true);// 设置画笔的锯齿效果
		int y = getHeight() / 2 - getProgressHeight() / 2;
		RectF bgRectF = new RectF(getProgressMargin(), y, getWidth() - getProgressMargin(), getProgressHeight() + y);// 设置个新的长方形
		canvas.drawRoundRect(bgRectF, getProgressHeight() / 2, getProgressHeight() / 2, p);// 第二个参数是x半径，第三个参数是y半径
	}

	private void drawProgressBar(Canvas canvas) {
		int y = getHeight() / 2 - getProgressHeight() / 2;
		Paint paint = new Paint();
		paint.setStyle(Paint.Style.FILL);// 充满
		paint.setColor(mProgressColor);
		paint.setAntiAlias(true);// 设置画笔的锯齿效果
		RectF progressRectF = new RectF(getProgressMargin(), y, mLastMotionX, getProgressHeight() + y);
		canvas.drawRoundRect(progressRectF, getProgressHeight() / 2, getProgressHeight() / 2, paint);
	}

	private void drawBitMap(Canvas canvas) {
		// drawBitmap
		Paint drawPaint = new Paint();
		// drawPaint.setStyle(Paint.Style.FILL);//充满
		drawPaint.setAntiAlias(true);// 设置画笔的锯齿效果
		float leftMark = mLastMotionX - markBitMap.getWidth() / 2;
		float topMark = getHeight() / 2 - getProgressHeight() / 2 - markBitMap.getHeight() - progressToThumbMargin;
		canvas.drawBitmap(markBitMap, leftMark, topMark, drawPaint);
	}

	private void drawPopupText(Canvas canvas) {
		String content = getPopupString();
		if (content == null)
			content = mCurProgress + "";

		float leftMark = mLastMotionX - markBitMap.getWidth() / 2;
		float topMark = getHeight() / 2 - getProgressHeight() / 2 - markBitMap.getHeight() - progressToThumbMargin;
		// drawText
		Paint paintText = new Paint();
		paintText.setAntiAlias(true);
		paintText.setStyle(Paint.Style.FILL);
		paintText.setColor(mPopupTextColor);
		paintText.setTextSize(mPopupTextSize);

		int w = (int) paintText.measureText(content);
		Paint.FontMetricsInt fontMetrics = paintText.getFontMetricsInt();
		int h = fontMetrics.bottom - fontMetrics.top;

		Rect targetRect = new Rect((int) leftMark, (int) topMark, (int) leftMark + markBitMap.getWidth(),
				(int) topMark + markBitMap.getHeight() - dip2px(getContext(), 2));

		int baseline = targetRect.top + (targetRect.bottom - targetRect.top - fontMetrics.bottom + fontMetrics.top) / 2
				- fontMetrics.top;
		// 下面这行是实现水平居中，drawText对应改为传入targetRect.centerX()

		paintText.setTextAlign(Paint.Align.CENTER);
		canvas.drawText(content, targetRect.centerX(), baseline, paintText);
	}

	private void drawMarkItemText(Canvas canvas) {

		int progressLen = getProgressLength();
		for (int i = 0; i < mMarkItemsNum; i++) {
			float pos = getProgressMargin() + i * progressLen / (mMarkItemsNum - 1);
			float bottom = getHeight() / 2 + getProgressHeight() / 2;
			//
			Paint paintText = new Paint();
			paintText.setAntiAlias(true);
			paintText.setStyle(Paint.Style.FILL);
			paintText.setColor(mMarkTextColor[i]);
			paintText.setTextSize(mMarkTextSize);
			//
			String content = mMarkDescArray[i];
			int w = (int) paintText.measureText(content);
			Paint.FontMetricsInt fontMetrics = paintText.getFontMetricsInt();
			int h = fontMetrics.bottom - fontMetrics.top;
			//
			Rect targetRect = new Rect((int) pos - w / 2, (int) bottom, (int) pos + w / 2, (int) bottom + h);
			//
			int baseline = targetRect.top
					+ (targetRect.bottom - targetRect.top - fontMetrics.bottom + fontMetrics.top) / 2 - fontMetrics.top;
			//
			paintText.setTextAlign(Paint.Align.CENTER);
			canvas.drawText(content, targetRect.centerX(), baseline, paintText);
			//
		}
	}

	@Override
	public boolean onTouchEvent(MotionEvent event) {

		switch (event.getAction() & MotionEvent.ACTION_MASK) {
		case MotionEvent.ACTION_DOWN:
			mLastMotionX = (int) event.getX();
			mLastPressedX = (int) event.getX();
			mLastPressedY = (int) event.getY();
			mPressedProgress = mCurProgress;
			break;
		case MotionEvent.ACTION_UP:
			if (mOnSeekBarChangeListener != null)
				mOnSeekBarChangeListener.OnProgressUpdated(getId(), mMin, mMax, mSpacing, mCurProgress);
			break;
		case MotionEvent.ACTION_POINTER_UP:

			break;
		case MotionEvent.ACTION_POINTER_DOWN:

			break;
		case MotionEvent.ACTION_MOVE:
			float topMark = getHeight() / 2 - getProgressHeight() / 2;
			Log.v(TAG, "topMark=" + topMark + "  event.getY()=" + event.getY());

			// 只允许手势在下方滑动
			// if (topMark > event.getY())
			// break;
			mLastMotionX = (int) event.getX();
			Log.v(TAG, "mLastMotionX=" + mLastMotionX);

			if (mLastMotionX < getProgressMargin())
				mLastMotionX = getProgressMargin();
			if (mLastMotionX > getWidth() - getProgressMargin())
				mLastMotionX = getWidth() - getProgressMargin();
			Log.v(TAG, "mLastMotionX 222 =" + mLastMotionX);

			int progressW = mLastMotionX - getProgressMargin();
			int bgW = getWidth() - getProgressMargin() * 2;
			mCurProgress = (int) ((float) progressW / (float) bgW * (float) mMax);

			if (mLastMotionX <= getProgressMargin())
				mLastMotionX = getProgressMargin() + mOffSet_X;

			if (mOnSeekBarChangeListener != null)
				mPopupString = mOnSeekBarChangeListener.OnPopupTextChanged(getId(), mMin, mMax, mSpacing, mCurProgress);

			postInvalidate();

			if (mOnSeekBarChangeListener != null)
				mOnSeekBarChangeListener.OnProgressChanged(getId(), mMin, mMax, mSpacing, mCurProgress);
			break;
		case MotionEvent.ACTION_CANCEL:
			if (mOnSeekBarChangeListener != null)
				mOnSeekBarChangeListener.OnProgressUpdated(getId(), mMin, mMax, mSpacing, mPressedProgress);
			break;
		}

		showPopup();

		return true;
	}

	public void setMarkTextColor(int index, int color) {
		mMarkTextColor[index] = color;
		postInvalidate();
	}

	public void reSetMarkTextColors() {
		mMarkTextColor = new int[mMarkItemsNum];
		for (int i = 0; i < mMarkTextColor.length; i++) {
			mMarkTextColor[i] = Color.BLACK;
		}
		postInvalidate();
	}

	private int getBackGroundColor() {
		return mBackGroundColor;
	}

	private void setBackGroundColor(int mBackGroundColor) {
		this.mBackGroundColor = mBackGroundColor;
	}

	private int getProgressColor() {
		return mProgressColor;
	}

	private void setProgressColor(int mProgressColor) {
		this.mProgressColor = mProgressColor;
	}

	private int getMarkTextColor() {
		return mPopupTextColor;
	}

	private void setMarkTextColor(int markTextColor) {
		this.mPopupTextColor = markTextColor;
	}

	private int getMarkTextSize() {
		return mPopupTextSize;
	}

	private void setMarkTextSize(int markTextSize) {
		this.mPopupTextSize = markTextSize;
	}

	public int getProgress() {
		return mCurProgress;
	}

	public void setProgress(int progress) {
		this.mCurProgress = progress;
		if (mOnSeekBarChangeListener != null)
			mPopupString = mOnSeekBarChangeListener.OnPopupTextChanged(getId(), mMin, mMax, mSpacing, mCurProgress);
		postInvalidate();
	}

	public void selectMarkItem(int selectItemNum) {
		if (selectItemNum >= mMarkItemsNum || selectItemNum <= -1)
			return;
		if (mOnSeekBarChangeListener != null) {
			mOnSeekBarChangeListener.OnPopupTextChanged(getId(), mMin, mMax, mSpacing, mCurProgress);
		} else {
			mPopupString = String.valueOf(selectItemNum * mSpacing);
		}
		setProgress(selectItemNum * mSpacing);
	}

	public int getSpacing() {
		return mSpacing;
	}

	private int getMax() {
		return mMax;
	}

	private void setMax(int max) {
		this.mMax = max;
		postInvalidate();
	}

	private int getLastMotionX() {
		return mLastMotionX;
	}

	private void setLastMotionX(int mLastMotionX) {
		this.mLastMotionX = mLastMotionX;
	}

	public int getOffSet_X() {
		return mOffSet_X;
	}

	public void setOffSet_X(int mOffestX) {
		this.mOffSet_X = mOffestX;
	}

	public void setProgressHeight(int progressHeight) {
		this.progressHeight = progressHeight;
	}

	public void setProgressMargin(int progressMargin) {
		this.progressMargin = progressMargin;
	}

	public Bitmap getMarkBitMap() {
		return markBitMap;
	}

	public void setMarkBitMap(Bitmap markBitMap) {
		this.markBitMap = markBitMap;
	}

	public int getMarkBitMapWidth() {
		return markBitMapWidth;
	}

	public void setMarkBitMapWidth(int markBitMapWidth) {
		this.markBitMapWidth = markBitMapWidth;
	}

	public int getMarkBitMapHeight() {
		return markBitMapHeight;
	}

	public void setMarkBitMapHeight(int markBitMapHeight) {
		this.markBitMapHeight = markBitMapHeight;
	}

	public int getProgressToThumbMargin() {
		return progressToThumbMargin;
	}

	public void setProgressToThumbMargin(int progressToThumemargin) {
		this.progressToThumbMargin = progressToThumemargin;
	}

	public int getStartProgressTextSize() {
		return mMarkTextSize;
	}

	public void setStartProgressTextSize(int startProgressTextSize) {
		this.mMarkTextSize = startProgressTextSize;
	}

	public void setOnSeekBarChangeListener(OnSeekBarChangeListener mOnSeekBarChangeListener) {
		this.mOnSeekBarChangeListener = mOnSeekBarChangeListener;
	}

	public String getPopupString() {
		return mPopupString;
	}

	private void setPopupString(String mPopupString) {
		this.mPopupString = mPopupString;
	}
}
