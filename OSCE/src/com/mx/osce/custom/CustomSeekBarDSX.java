//package com.mx.osce.custom;
//
//import com.mx.osce.R;
//
//import android.content.Context;
//import android.content.res.TypedArray;
//import android.graphics.Canvas;
//import android.graphics.Color;
//import android.graphics.Paint;
//import android.graphics.Rect;
//import android.util.AttributeSet;
//import android.widget.SeekBar;
//import android.widget.SeekBar.OnSeekBarChangeListener;
//
//public class CustomSeekBarDSX {
//	private CharSequence[] scaleArr;
//	private Paint paint;
//	private Rect textBoundRect;
//	private int scalePadding = 4;
//	private int scaleLineHeight = 10;
//	private int scaleTextPadding = 15;
//	private int scaleTextColor = Color.GRAY;
//	private int scaleLineColor = Color.GRAY;
//	private OnSeekBarChangeListener onSeekBarChangeListener;
//	private float seperator;
//	private int scaleProgress = 0;
//
//	public CustomSeekBarDSX(Context context, AttributeSet attrs) {
//		super(context, attrs);
//		TypedArray typeArray = context.obtainStyledAttributes(attrs, R.styleable.MyCustomSeekBar);
//		scaleLineColor = typeArray.getColor(R.styleable.MyCustomSeekBar_scaleColor, Color.GRAY);
//		scaleTextColor = typeArray.getColor(R.styleable.MyCustomSeekBar_scaleTextColor, Color.GRAY);
//		scalePadding = (int) typeArray.getDimension(R.styleable.MyCustomSeekBar_scalePadding, 4);
//		scaleLineHeight = (int) typeArray.getDimension(R.styleable.MyCustomSeekBar_scaleLineHeight, 10);
//		scaleTextPadding = (int) typeArray.getDimension(R.styleable.MyCustomSeekBar_scaleTextPadding, 15);
//		scaleProgress = typeArray.getInt(R.styleable.MyCustomSeekBar_scaleProgress, 0);
//		scaleArr = typeArray.getTextArray(R.styleable.MyCustomSeekBar_scaleEntries);
//		if (scaleArr == null || scaleArr.length <= 0) {
//			scaleArr = new String[] { "6", "12", "24", "48" };
//		}
//		typeArray.recycle();
//		init();
//	}
//
//	public CustomSeekBarDSX(Context context) {
//		super(context);
//
//		init();
//	}
//
//	private void init() {
//		paint = new Paint();
//		paint.setAntiAlias(true);
//		textBoundRect = new Rect();
//		setOnSeekBarChangeListener(this);
//	}
//
//	@Override
//	protected synchronized void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
//		// int size=MeasureSpec.getSize(heightMeasureSpec);
//		// int scaleHeight = scalePadding + scaleLineHeight +
//		// textBoundRect.height();
//		// size = scaleHeight + size / 2 ;
//		// heightMeasureSpec = MeasureSpec.makeMeasureSpec(size,
//		// MeasureSpec.EXACTLY);
//		super.onMeasure(widthMeasureSpec, heightMeasureSpec);
//
//	}
//
//	@Override
//	protected void onLayout(boolean changed, int left, int top, int right, int bottom) {
//		super.onLayout(changed, left, top, right, bottom);
//		int factWidth = getWidth() - getPaddingLeft() - getPaddingRight();
//		seperator = (float) factWidth / scaleArr.length;
//		float scaleProgressLength = (float) getMax() / scaleArr.length;
//		setProgress((int) ((scaleProgress + 0.5) * scaleProgressLength));
//	}
//
//	@Override
//	protected synchronized void onDraw(Canvas canvas) {
//		canvas.save();
//		drawScale(canvas);
//		canvas.restore();
//		super.onDraw(canvas);
//	}
//
//	@Override
//	protected void onFinishInflate() {
//		super.onFinishInflate();
//	}
//
//	private void drawScale(Canvas canvas) {
//		// int factWidth = getWidth() - getPaddingLeft() - getPaddingRight();
//		// seperator = (float) factWidth / scaleArr.length;
//		for (int i = 0; i < scaleArr.length; i++) {
//			canvas.save();
//			canvas.translate(i * seperator + getPaddingLeft() + seperator / 2,
//					getHeight() / 2 + scalePadding + scaleLineHeight);
//			paint.setColor(scaleLineColor);
//			canvas.drawLine(0, 0, 0, 10, paint);
//			String scale = scaleArr[i] + "";
//			paint.getTextBounds(scale, 0, scale.length(), textBoundRect);
//			paint.setColor(scaleTextColor);
//			canvas.drawText(scale, -textBoundRect.width() / 2, textBoundRect.height() + scaleTextPadding, paint);
//			canvas.restore();
//		}
//	}
//
//	private void setScaleProgress(int progress) {
//		float scaleProgress = (float) getMax() / scaleArr.length;
//		// 确定progress当前位置
//		int scaleRange = (int) Math.floor(progress / scaleProgress);
//		// scaleRange = scaleRange <= 0 ? 1 : scaleRange;
//		scaleRange = scaleRange >= scaleArr.length ? scaleArr.length - 1 : scaleRange;
//		setProgress((int) ((scaleRange + 0.5) * scaleProgress));
//	}
//
//	@Override
//	public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
//		if (onSeekBarChangeListener != null) {
//			onSeekBarChangeListener.onProgressChanged(seekBar, progress, fromUser);
//		}
//	}
//
//	@Override
//	public void onStartTrackingTouch(SeekBar seekBar) {
//		if (onSeekBarChangeListener != null) {
//			onSeekBarChangeListener.onStartTrackingTouch(seekBar);
//		}
//	}
//
//	@Override
//	public void onStopTrackingTouch(SeekBar seekBar) {
//		setScaleProgress(getProgress());
//		if (onSeekBarChangeListener != null) {
//			onSeekBarChangeListener.onStopTrackingTouch(seekBar);
//		}
//	}
//
//}
