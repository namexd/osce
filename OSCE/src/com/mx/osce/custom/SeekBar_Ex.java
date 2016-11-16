package com.mx.osce.custom;

import static android.view.View.MeasureSpec.makeMeasureSpec;

import com.mx.osce.R;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Point;
import android.os.Handler;
import android.util.AttributeSet;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.PopupWindow;
import android.widget.SeekBar;
import android.widget.TextView;

/**
 * Created by misrobot on 2016/3/25.
 */
public class SeekBar_Ex extends SeekBar implements SeekBar.OnSeekBarChangeListener {
	private int mPopupWidth;
	private int mPopupStyle;
	public static final int POPUP_FIXED = 1;
	public static final int POPUP_FOLLOW = 0;
	private PopupWindow mPopup;
	private View mPopupView;
	private TextView mPopupTextView;

	private int mYLocationOffset;
	private OnSeekBarChangeListener mInternalListener;
	private OnSeekBarChangeListener mExternalListener;
	private Handler handler = new Handler();

	private OnSeekBarHintProgressChangeListener mProgressChangeListener;

	public interface OnSeekBarHintProgressChangeListener {
		public String onHintTextChanged(SeekBar_Ex seekBarHint, int progress);
	}

	public SeekBar_Ex(Context context) {
		super(context);
		init(context, null);
	}

	public SeekBar_Ex(Context context, AttributeSet attrs, int defStyle) {
		super(context, attrs, defStyle);
		init(context, attrs);
	}

	@Override
	public boolean isInEditMode() {
		return true;
	}

	public SeekBar_Ex(Context context, AttributeSet attrs) {
		super(context, attrs);
		init(context, attrs);
	}

	private void init(Context context, AttributeSet attrs) {

		setOnSeekBarChangeListener(this);

		TypedArray a = context.obtainStyledAttributes(attrs, R.styleable.CustomSeekBar);

		mPopupWidth = (int) a.getDimension(R.styleable.CustomSeekBar_popupWidth, ViewGroup.LayoutParams.WRAP_CONTENT);
		mYLocationOffset = (int) a.getDimension(R.styleable.CustomSeekBar_yOffset, 0);
		mPopupStyle = a.getInt(R.styleable.CustomSeekBar_popupStyle, POPUP_FOLLOW);

		a.recycle();
		initHintPopup();
	}

	public void setPopupStyle(int style) {
		mPopupStyle = style;
	}

	public int getPopupStyle() {
		return mPopupStyle;
	}

	private void initHintPopup() {
		String popupText = null;
		if (mProgressChangeListener != null) {
			popupText = mProgressChangeListener.onHintTextChanged(this, getProgress());
		}
		LayoutInflater inflater = (LayoutInflater) getContext().getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		mPopupView = inflater.inflate(R.layout.popup, null);
		mPopupView.measure(makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED),
				makeMeasureSpec(0, View.MeasureSpec.UNSPECIFIED));
		mPopupTextView = (TextView) mPopupView.findViewById(R.id.text);
		mPopupTextView.setText(popupText != null ? popupText : String.valueOf(getProgress()));
		mPopup = new PopupWindow(mPopupView, ViewGroup.LayoutParams.WRAP_CONTENT, ViewGroup.LayoutParams.WRAP_CONTENT,
				false);
		mPopup.setAnimationStyle(R.style.popup_animation);
	}

	public void showPopup() {
		if (mPopupStyle == POPUP_FOLLOW) {
			Point offsetPoint = getFollowHintOffset();
			mPopup.showAtLocation(this, Gravity.NO_GRAVITY, 0, 0);
			mPopup.update(this, offsetPoint.x - 5, offsetPoint.y + 5, -1, -1);
		}
		if (mPopupStyle == POPUP_FIXED) {
			mPopup.showAtLocation(this,
					Gravity.NO_GRAVITY /* | Gravity.BOTTOM */, 0 - mPopup.getWidth() / 2,
					(int) (this.getY() + mYLocationOffset + this.getHeight()));
		}
	}

	public void hidePopup() {
		handler.removeCallbacksAndMessages(null);
		if (mPopup.isShowing()) {
			mPopup.dismiss();
		}
	}

	@Override
	public void setOnSeekBarChangeListener(OnSeekBarChangeListener l) {
		if (mInternalListener == null) {
			mInternalListener = l;
			super.setOnSeekBarChangeListener(l);
		} else {
			mExternalListener = l;
		}
	}

	public void setOnProgressChangeListener(OnSeekBarHintProgressChangeListener l) {
		mProgressChangeListener = l;
	}

	@Override
	public void onProgressChanged(SeekBar seekBar, int progress, boolean b) {
		String popupText = null;
		if (mProgressChangeListener != null) {
			popupText = mProgressChangeListener.onHintTextChanged(this, getProgress());
		}

		if (mExternalListener != null) {
			mExternalListener.onProgressChanged(seekBar, progress, b);
		}

		mPopupTextView.setText(popupText != null ? popupText : String.valueOf(progress));
		if (mPopupStyle == POPUP_FOLLOW) {
			Point offsetPoint = getFollowHintOffset();
			mPopup.update(this, offsetPoint.x, offsetPoint.y, -1, -1);
		}
	}

	@Override
	public void onStartTrackingTouch(SeekBar seekBar) {
		if (mExternalListener != null) {
			mExternalListener.onStartTrackingTouch(seekBar);
		}

		showPopup();
	}

	@Override
	public void onStopTrackingTouch(SeekBar seekBar) {
		if (mExternalListener != null) {
			mExternalListener.onStopTrackingTouch(seekBar);
		}
		// TODO Needn't Hide Popup.
		// hidePopup();
	}

	public int getProgress() {
		return super.getProgress();
	}

	public void setProgress(int progress) {
		if (progress > getMax())
			throw new RuntimeException("The setting progress is larger than the Max!");

		super.setProgress(progress);

		if (this.isShown()) {
			if (!mPopup.isShowing()) {
				mPopup.showAtLocation(this, Gravity.NO_GRAVITY, 0, 0);
				this.onProgressChanged(this, progress, true);
			}
		} else {// TODO If the SeekBar not Shown.
			// String popupText = String.valueOf(progress);
			// mPopupTextView.setText(popupText != null ? popupText :
			// String.valueOf(progress));
			//
			// if (mPopupStyle == POPUP_FOLLOW) {
			// mPopup.update((int) (this.getX() + (int) getXPosition(this)),
			// (int) (this.getY() + mYLocationOffset + this.getHeight()),
			// -1,
			// -1);
			// }
			// handler.post(new Runnable() {
			// @Override
			// public void run() {
			//// showPopupInternally();
			// Point offsetPoint = getFollowHintOffset();
			// mPopup.showAtLocation(SeekBar_Ex.this, Gravity.NO_GRAVITY, 0, 0);
			// mPopup.update(offsetPoint.x, offsetPoint.y, -1, -1);
			// }
			// });
		}
	}

	private Point getFollowHintOffset() {
		int xOffset = getHorizontalOffset(this.getProgress());
		int yOffset = getVerticalOffset();
		return new Point(xOffset, yOffset);
	}

	private int getHorizontalOffset(int progress) {
		return getFollowPosition(progress) - mPopupView.getMeasuredWidth() / 2;
	}

	private int getVerticalOffset() {
		return -(getRealHeight() + mPopupView.getMeasuredHeight() + this.getRealHeight() / 2);
	}

	protected int getFollowPosition(int progress) {
		return (int) (progress * (this.getWidth() - this.getPaddingLeft() - this.getPaddingRight())
				/ (float) this.getMax());
	}

	private int getRealHeight() {
		return this.getHeight() - this.getPaddingBottom() - this.getPaddingTop();
	}
}