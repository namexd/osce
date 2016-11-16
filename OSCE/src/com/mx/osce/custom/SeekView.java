package com.mx.osce.custom;

import java.math.BigDecimal;

import com.mx.osce.R;

import android.content.Context;
import android.content.res.Resources;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.drawable.BitmapDrawable;
import android.graphics.drawable.Drawable;
import android.util.AttributeSet;
import android.view.MotionEvent;
import android.view.View;
import android.view.View.MeasureSpec;


public class SeekView extends View {
	private static final String TAG = "SeekBarPressure";
	private static final int CLICK_ON_LOW = 1; // 鐐瑰嚮鍦ㄥ墠婊戝潡涓�
	private static final int CLICK_ON_HIGH = 2; // 鐐瑰嚮鍦ㄥ悗婊戝潡涓�
	private static final int CLICK_IN_LOW_AREA = 3;
	private static final int CLICK_IN_HIGH_AREA = 4;
	private static final int CLICK_OUT_AREA = 5;
	private static final int CLICK_INVAILD = 0;
	private static final int[] STATE_NORMAL = {};
	private static final int[] STATE_PRESSED = { android.R.attr.state_pressed, android.R.attr.state_window_focused, };
	private Drawable hasScrollBarBg; // 婊戝姩鏉℃粦鍔ㄥ悗鑳屾櫙鍥�
	private Drawable notScrollBarBg; // 婊戝姩鏉℃湭婊戝姩鑳屾櫙鍥�
	private Drawable mThumbLow; // 鍓嶆粦鍧�

	private int mScollBarWidth; // 鎺т欢瀹藉害=婊戝姩鏉″搴�+婊戝姩鍧楀搴�
	private int mScollBarHeight; // 婊戝姩鏉￠珮搴�
	int finalposition = 0;
	private int mThumbWidth; // 婊戝姩鍧楀搴�
	private int mThumbHeight; // 婊戝姩鍧楅珮搴�

	private double mOffsetLow = 0; // 鍓嶆粦鍧椾腑蹇冨潗鏍�
	private int mDistance = 0; // 鎬诲埢搴︽槸鍥哄畾璺濈 涓よ竟鍚勫幓鎺夊崐涓粦鍧楄窛绂�

	private int mThumbMarginTop = 40; // 婊戝姩鍧楅《閮ㄨ窛绂讳笂杈规璺濈锛屼篃灏辨槸璺濈瀛椾綋椤堕儴鐨勮窛绂�

	private int mFlag = CLICK_INVAILD;
	private OnSeekBarChangeListener mBarChangeListener;
	private static final int PART_ITEM = 1;// 鍗婂皬 鍗犵殑鍒嗘暟
	private float mPartWidth; // 姣忎竴灏忎唤鐨勫搴�
	private static int MAX = 10;
	public static final int SHORTLINE_HEIGHT = 5; // 鐭嚎鐨勯珮搴� 锛堢敾鍒诲害鏃朵細鏈夐暱鐭嚎锛�
	public static final int LONGLINE_HEIGHT = 10; // 闀跨嚎鐨勯珮搴�
	private static int degs[]={0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,
			46,47,48,49,50,51,52,53} ;
//	public static void setdegs(){
//		degs = new int[MAX+1];
//
//		for (int i = 0; i <= MAX+1 ; i++) {
//
//			degs[i] = i;
//		}
//	};
	
	public double defaultScreenLow = 0; // 榛樿鍓嶆粦鍧椾綅缃櫨鍒嗘瘮
	private boolean isEdit = false; // 杈撳叆妗嗘槸鍚︽鍦ㄨ緭鍏�
	private boolean canscoll = true;
	private boolean OnlyOne = false;


	public SeekView(Context context) {
		this(context, null);

	}

	public SeekView(Context context, AttributeSet attrs) {
		this(context, attrs, 0);
	}

	public SeekView(Context context, AttributeSet attrs, int defStyle) {
		super(context, attrs, defStyle);
		// this.setBackgroundColor(Color.BLACK);
		
		Resources resources = getResources();
		notScrollBarBg = resources.getDrawable(R.drawable.seekbarpressure_bg_progress);
		hasScrollBarBg = resources.getDrawable(R.drawable.seekbarpressure_bg_normal);
		mThumbLow = resources.getDrawable(R.drawable.seekbar_thumb);
		mThumbLow.setState(STATE_NORMAL);

		mScollBarWidth = notScrollBarBg.getIntrinsicWidth();
		mScollBarHeight = notScrollBarBg.getIntrinsicHeight();

		mThumbWidth = mThumbLow.getIntrinsicWidth();
		mThumbHeight = mThumbLow.getIntrinsicHeight();

	}

	// 榛樿鎵ц锛岃绠梫iew鐨勫楂�,鍦╫nDraw()涔嬪墠
	protected void onMeasure(int widthMeasureSpec, int heightMeasureSpec) {
		// int width = measureWidth(widthMeasureSpec);
		// int height = measureHeight(heightMeasureSpec);
		int width = 800;
		mScollBarWidth = width;

		mOffsetLow = mThumbWidth / 2;
		mDistance = width - mThumbWidth;
		mOffsetLow = formatDouble(defaultScreenLow / 100 * (mDistance)) + mThumbWidth / 2;
		setMeasuredDimension(width, mThumbHeight + 2 * mThumbMarginTop);
	}

	private int measureWidth(int measureSpec) {
		int specMode = MeasureSpec.getMode(measureSpec);
		int specSize = MeasureSpec.getSize(measureSpec);
		// wrap_content
		if (specMode == MeasureSpec.AT_MOST) {
		}
		// fill_parent鎴栬�呯簿纭��
		else if (specMode == MeasureSpec.EXACTLY) {
		}

		return specSize;
	}

	protected void onLayout(boolean changed, int l, int t, int r, int b) {
		super.onLayout(changed, l, t, r, b);
	}

	protected void onDraw(Canvas canvas) {
		super.onDraw(canvas);

		int aaa = mThumbMarginTop + mThumbHeight / 2 - mScollBarHeight / 2;
		int bbb = aaa + mScollBarHeight;

		// 鐧借壊锛屼笉浼氬姩
		notScrollBarBg.setBounds(mThumbWidth / 2, aaa, mScollBarWidth - mThumbWidth / 2, bbb);
		notScrollBarBg.draw(canvas);

		// 钃濊壊锛屼腑闂撮儴鍒嗕細鍔�
		hasScrollBarBg.setBounds(mThumbWidth / 2, aaa, (int) mOffsetLow, bbb);
		hasScrollBarBg.draw(canvas);

		// 鍓嶆粦鍧�
		mThumbLow.setBounds((int) (mOffsetLow - mThumbWidth / 2), mThumbMarginTop, (int) (mOffsetLow + mThumbWidth / 2),
				mThumbHeight + mThumbMarginTop);
		mThumbLow.draw(canvas);

		double progressLow = formatDouble((mOffsetLow - mThumbWidth / 2) * 100 / mDistance);

		// Log.d(TAG, "onDraw-->mOffsetLow: " + mOffsetLow + " mOffsetHigh: " +
		// mOffsetHigh + " progressLow: " + progressLow + " progressHigh: " +
		// progressHigh);
		// canvas.drawText((int) progressLow + "", (int)mOffsetLow - 2 - 2, 15,
		// text_Paint);

		if (mBarChangeListener != null) {
			if (!isEdit) {
				mBarChangeListener.onProgressChanged(this, progressLow);
			}

		}
		drawRule(canvas);
		drawRodPlaceValue(canvas, (int) mOffsetLow);
	}

	protected void drawRodPlaceValue(Canvas canvas, int mOffsetLow) {

		Paint paint = new Paint();
		Drawable c = getResources().getDrawable(R.drawable.orange);
		BitmapDrawable bd = (BitmapDrawable) c;
		canvas.drawBitmap(bd.getBitmap(), mOffsetLow - mThumbWidth / 2 - 2, 10, paint);

		paint.setColor(Color.WHITE);
		paint.setTextAlign(Paint.Align.CENTER);
		paint.setTextSize(15);
		if (OnlyOne) {
			canvas.drawText(finalposition + "", mOffsetLow, c.getIntrinsicHeight() / 2 + 15, paint);
		} else {
			canvas.drawText(finalposition + 1 + "", mOffsetLow, c.getIntrinsicHeight() / 2 + 15, paint);
		}
	}

	/**
	 * 鐢诲埢搴﹀昂
	 * 
	 * @param canvas
	 */
	protected void drawRule(Canvas canvas) {
		Paint paint = new Paint();
		paint.setStrokeWidth(1);
		paint.setColor(getResources().getColor(R.color.bg_lin_95));
		paint.setTextSize(20);
		paint.setTextAlign(Paint.Align.CENTER);
		paint.setAntiAlias(true);
		int mPartWidth = (mScollBarWidth - mThumbWidth) / MAX;
		// 涓�娆￠亶鍘嗕袱浠�,缁樺埗鐨勪綅缃兘鏄湪濂囨暟浣嶇疆
		for (int i = 0; i <= MAX; i += 1) {

			float degX = mThumbWidth / 2 + i * mPartWidth;
			int degY;

			if (i % PART_ITEM == 0) {
				degY = (mScollBarHeight + mThumbMarginTop + 8) + DensityUtil.dip2px(getContext(), LONGLINE_HEIGHT);
				if (OnlyOne) {
					canvas.drawText(degs[i] + "", degX, degY + DensityUtil.dip2px(getContext(), LONGLINE_HEIGHT) + 3,
							paint);
				} else {
					canvas.drawText(degs[i + 1] + "", degX,
							degY + DensityUtil.dip2px(getContext(), LONGLINE_HEIGHT) + 3, paint);
				}
			} else {
				degY = (mScollBarHeight + mThumbMarginTop + 8) + DensityUtil.dip2px(getContext(), SHORTLINE_HEIGHT);
			}
			canvas.drawLine(degX, (mScollBarHeight + mThumbMarginTop + 6), degX, degY, paint);
		}
	}

	@Override
	public boolean onTouchEvent(MotionEvent e) {
		// 鎸変笅
		if (!canscoll)
			return false;
		if (e.getAction() == MotionEvent.ACTION_DOWN) {
			if (mBarChangeListener != null) {
				mBarChangeListener.onProgressBefore();
				isEdit = false;
			}
			mFlag = getAreaFlag(e);
			// Log.d(TAG, "e.getX: " + e.getX() + "mFlag: " + mFlag);
			// Log.d("ACTION_DOWN", "------------------");
			if (mFlag == CLICK_ON_LOW) {
				mThumbLow.setState(STATE_PRESSED);
			} else if (mFlag == CLICK_IN_LOW_AREA) {
				mThumbLow.setState(STATE_PRESSED);
				// 濡傛灉鐐瑰嚮0-mThumbWidth/2鍧愭爣
				if (e.getX() < 0 || e.getX() <= mThumbWidth / 2) {
					mOffsetLow = mThumbWidth / 2;
				} else if (e.getX() > mScollBarWidth - mThumbWidth / 2) {
					// mOffsetLow = mDistance - mDuration;
					mOffsetLow = mThumbWidth / 2 + mDistance;
				} else {
					mOffsetLow = formatDouble(e.getX());
					// if (mOffsetHigh<= mOffsetLow) {
					// mOffsetHigh = (mOffsetLow + mDuration <= mDistance) ?
					// (mOffsetLow + mDuration)
					// : mDistance;
					// mOffsetLow = mOffsetHigh - mDuration;
					// }
				}
			}
			// 璁剧疆杩涘害鏉�
			refresh();

			// 绉诲姩move
		} else if (e.getAction() == MotionEvent.ACTION_MOVE) {
			// Log.d("ACTION_MOVE", "------------------");
			if (mFlag == CLICK_ON_LOW) {
				if (e.getX() < 0 || e.getX() <= mThumbWidth / 2) {
					mOffsetLow = mThumbWidth / 2;
				} else if (e.getX() >= mScollBarWidth - mThumbWidth / 2) {
					mOffsetLow = mThumbWidth / 2 + mDistance;
				} else {
					mOffsetLow = formatDouble(e.getX());

				}
			}
			// 璁剧疆杩涘害鏉�
			refresh();
			// 鎶捣
		} else if (e.getAction() == MotionEvent.ACTION_UP) {
			// Log.d("ACTION_UP", "------------------");
			mThumbLow.setState(STATE_NORMAL);

			if (mBarChangeListener != null) {
				mBarChangeListener.onProgressChanged(this,
						formatDouble((mOffsetLow - mThumbWidth / 2) * 100 / mDistance));
			}
			// 杩欎袱涓猣or寰幆 鏄敤鏉ヨ嚜鍔ㄥ榻愬埢搴︾殑锛屾敞閲婂悗锛屽氨鍙互鑷敱婊戝姩鍒颁换鎰忎綅缃�

			for (int i = 0; i <= MAX; i++) {
				int j = MAX - 1;
				if (j == 0)
					j = 1;
				if (Math.abs(mOffsetLow - i * ((mScollBarWidth - mThumbWidth) / MAX)) <= (mScollBarWidth - mThumbWidth)
						/ (j) / 2) {
					System.out.println(i);
					mOffsetLow = i * ((mScollBarWidth - mThumbWidth) / MAX) + mThumbWidth / 2;
					invalidate();
					finalposition = i;
					break;
				}
			}
			if (mBarChangeListener != null) {
				mBarChangeListener.onProgressAfter(finalposition);
			}

			//
			// for (int i = 0; i < money.length; i++) {
			// if(Math.abs(mOffsetHigh-i*
			// ((mScollBarWidth-mThumbWidth)/(money.length-1)
			// ))<(mScollBarWidth-mThumbWidth)/(money.length-1)/2){
			// mprogressHigh=i;
			// mOffsetHigh =i* ((mScollBarWidth-mThumbWidth)/(money.length-1));
			// invalidate();
			// break;
			// }
			// }
		}
		return true;
	}

	// 鏇存柊婊戝潡
	private void refresh() {
		invalidate();
	}

	// 璁剧疆鍓嶆粦鍧楃殑鍊�
	public void setProgressLow(double progressLow) {
		this.defaultScreenLow = progressLow;
		mOffsetLow = formatDouble(progressLow / 100 * (mDistance)) + mThumbWidth / 2;
		isEdit = true;
		refresh();
	}

	// 璁剧疆鍓嶆粦鍧楃殑鍊�
	public void setProgressBypositon(int position) {

		this.defaultScreenLow = 100 / MAX * position;
		finalposition = position;
		mOffsetLow = position * ((mScollBarWidth - mThumbWidth) / (MAX)) + mThumbWidth / 2;
		isEdit = true;

		refresh();
	}

	public void setOnSeekBarChangeListener(OnSeekBarChangeListener mListener) {
		this.mBarChangeListener = mListener;
	}

	public int getAreaFlag(MotionEvent e) {

		int top = mThumbMarginTop;
		int bottom = mThumbHeight + mThumbMarginTop;
		if (e.getY() >= top && e.getY() <= bottom && e.getX() >= (mOffsetLow - mThumbWidth / 2)
				&& e.getX() <= mOffsetLow + mThumbWidth / 2) {
			return CLICK_ON_LOW;
		} else
			if (e.getY() >= top && e.getY() <= bottom && ((e.getX() >= 0 && e.getX() < (mOffsetLow - mThumbWidth / 2))
					|| ((e.getX() > (mOffsetLow + mThumbWidth / 2))))) {
			return CLICK_IN_LOW_AREA;
		} else if (!(e.getX() >= 0 && e.getX() <= mScollBarWidth && e.getY() >= top && e.getY() <= bottom)) {
			return CLICK_OUT_AREA;
		} else {
			return CLICK_INVAILD;
		}
	}

	public interface OnSeekBarChangeListener {
		// 婊戝姩鍓�
		public void onProgressBefore();

		// 婊戝姩鏃�
		public void onProgressChanged(SeekView seekView, double progressLow);

		// 婊戝姩鍚�
		public void onProgressAfter(int i);
	}

	public static double formatDouble(double pDouble) {
		BigDecimal bd = new BigDecimal(pDouble);
		BigDecimal bd1 = bd.setScale(2, BigDecimal.ROUND_HALF_UP);
		pDouble = bd1.doubleValue();
		return pDouble;
	}

	public int getFinalposition() {
		return finalposition;
	}

	public void setFinalposition(int finalposition) {
		this.finalposition = finalposition;
	}

	public int getMAX() {

		return MAX;
	}

	public void setMAX(int mAX) {
		MAX = mAX;

		if (mAX == 0) {
			MAX += 1;
			canscoll = false;
			OnlyOne = true;
		}
		

		refresh();
	}

}
