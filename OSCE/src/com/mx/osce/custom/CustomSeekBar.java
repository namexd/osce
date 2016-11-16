package com.mx.osce.custom;


import android.annotation.TargetApi;
import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.os.Build;
import android.text.TextUtils;
import android.util.AttributeSet;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.SeekBar;
import android.widget.TextView;


import static android.view.View.MeasureSpec.makeMeasureSpec;

import com.mx.osce.R;

/**
 * Created by misrobot on 2016/3/29.
 */
public class CustomSeekBar extends LinearLayout {
    private static final String TAG = CustomSeekBar.class.getSimpleName();
    private static final int DEFAULT_MAX = 80;
    private static final int DEFAULT_MAX_ITEM_NUM = 9;
    private static final int DEFAULT_MARK_TEXT_SIZE = 15;
    private static final int DEFAULT_MARK_TEXT_COLOR = 0xFF0000FF;
    private static final int DEFAULT_SEEK_BAR_PADDING_LEN = 0;
    private LinearLayout mTopLayout;
    private LinearLayout mBottomLayout;
    private SeekBar_Ex mSeekBar;
    //
    private int mSeekBarPaddingLen = DEFAULT_SEEK_BAR_PADDING_LEN;
    //根据 mMarkItemNum 来选择，整除(mMarkItemNum - 1)最好
    private int mMax = DEFAULT_MAX;
    //刻度的文字大小及颜色
    private float mMarkTextSize = DEFAULT_MARK_TEXT_SIZE;
    private int mMarkTextColor = DEFAULT_MARK_TEXT_COLOR;
    //刻度的数目，默认9
    private int mMarkItemNum = DEFAULT_MAX_ITEM_NUM;
    //相邻刻度的间距
    protected int mSpacing = mMax / (mMarkItemNum - 1);
    //当前选中的刻度
    private int nowMarkItem;
    //刻度的下标名称数组
    private String[] mMarkDescArray = new String[]{"0", "1", "2", "3", "4", "5", "6", "7", "8"};
    //
    private OnSelectItemListener mSelectItemListener;
    //是否显示刻度指示点
    private boolean isShowPoint = true;
    //SeekBar的ProgressDrawable
    private Drawable mProgressDrawable = null;
    //SeekBar的Thumb
    private Drawable mThumbDrawable = null;
    //刻度形状
    private Drawable mMarkDrawable = null;

    public CustomSeekBar(Context context) {
        super(context);

        initView();
    }

    public CustomSeekBar(Context context, AttributeSet attrs) {
        super(context, attrs);
        //
        initPara(context, attrs);
        //
        initView();
    }

    @TargetApi(Build.VERSION_CODES.HONEYCOMB)
    public CustomSeekBar(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        //
        initPara(context, attrs);
        //
        initView();
    }

    private void initPara(Context context, AttributeSet attrs) {

        TypedArray typedArray = context.obtainStyledAttributes(attrs, R.styleable.CustomSeekBar);

        final int N = typedArray.getIndexCount();

        for (int i = 0; i < N; i++) {
            int attr = typedArray.getIndex(i);
            //
            if (attr == R.styleable.CustomSeekBar_markItemNum) {
                this.mMarkItemNum = typedArray.getInteger(attr, DEFAULT_MAX_ITEM_NUM);
            } else if (attr == R.styleable.CustomSeekBar_max) {
                this.mMax = typedArray.getInteger(attr, DEFAULT_MAX);
            } else if (attr == R.styleable.CustomSeekBar_markDescArray) {
                String content = typedArray.getString(attr);
                if (!TextUtils.isEmpty(content)) {
                    String[] strings = content.split("\\|");//以|作为分隔符.
                    this.mMarkDescArray = strings;
                }
            } else if (attr == R.styleable.CustomSeekBar_isShowPoint) {
                this.isShowPoint = typedArray.getBoolean(attr, false);
            } else if (attr == R.styleable.CustomSeekBar_progressDrawable) {
                Drawable drawable = typedArray.getDrawable(attr);
                if (drawable != null) {
                    this.mProgressDrawable = typedArray.getDrawable(attr);
                }
            } else if (attr == R.styleable.CustomSeekBar_thumbDrawable) {
                Drawable drawable = typedArray.getDrawable(attr);
                if (drawable != null) {
                    this.mThumbDrawable = drawable;
                }
            } else if (attr == R.styleable.CustomSeekBar_markDrawable) {
                Drawable drawable = typedArray.getDrawable(attr);
                if (drawable != null) {
                    this.mMarkDrawable = drawable;
                }
            } else if (attr == R.styleable.CustomSeekBar_markTextSize) {
                this.mMarkTextSize = ScreenUtils.pxToDp(getContext(), typedArray.getDimensionPixelSize(attr, DEFAULT_MARK_TEXT_SIZE));
            } else if (attr == R.styleable.CustomSeekBar_markTextColor) {
                mMarkTextColor = typedArray.getInteger(attr, DEFAULT_MARK_TEXT_COLOR);
            } else if (attr == R.styleable.CustomSeekBar_seekBarPaddingLen) {
                mSeekBarPaddingLen = typedArray.getInteger(attr, DEFAULT_SEEK_BAR_PADDING_LEN);
            }
        }
        this.mSpacing = mMax / (mMarkItemNum - 1);

        typedArray.recycle();

        Log.i(TAG, "Init parameter finished...");
    }

    private void initView() {
        //
        if (this.mMarkDescArray.length < this.mMarkItemNum) {
            throw new RuntimeException("The name of the scale of the array length can not be less than the number of scales!");
        }
        //
        this.setOrientation(LinearLayout.VERTICAL);
        this.setGravity(Gravity.CENTER);
        //设置ProgressDrawable
        if (mProgressDrawable != null) {
            this.mSeekBar.setProgressDrawable(mProgressDrawable);
        }
        //设置Thumb
        if (mThumbDrawable != null) {
            this.mSeekBar.setThumb(mThumbDrawable);
        }
        //
//        this.mSeekBar.setOnSeekBarChangeListener(new SeekBar_Ex.OnSeekBarChangeListener() {
//            private int shouldInProgress;
//
//            @Override
//            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
//                //
//                nowMarkItem = Math.round(progress / mSpacing);
//
//                shouldInProgress = mSpacing * nowMarkItem;
//                //
//                CustomSeekBar.this.mSeekBar.setProgress(shouldInProgress);
//                //
//                if (DEBUG) {
//                    Log.e(TAG, "progress---" + progress);
//                }
//            }
//
//            @Override
//            public void onStartTrackingTouch(SeekBar seekBar) {
//
//            }
//
//            @Override
//            public void onStopTrackingTouch(SeekBar seekBar) {
//                if (DEBUG) {
//                    Log.e(TAG, "shouldInProgress---" + shouldInProgress);
//                }
//                //
//                if (mSelectItemListener != null) {
//                    mSelectItemListener.selectItem(nowMarkItem, mMarkDescArray[nowMarkItem]);
//                }
//            }
//        });
        //
        mTopLayout = new LinearLayout(getContext());
        LayoutParams TopLP = new LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT);
        mTopLayout.setLayoutParams(TopLP);
        mTopLayout.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
        mTopLayout.setOrientation(LinearLayout.HORIZONTAL);

        mBottomLayout = new LinearLayout(getContext());
        LayoutParams BottomLP = new LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT);
        BottomLP.setMargins(0, (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,
                1, getResources().getDisplayMetrics()), 0, 0);
        mBottomLayout.setLayoutParams(BottomLP);
        mBottomLayout.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
        mBottomLayout.setOrientation(LinearLayout.HORIZONTAL);
        //
        addAllMarkItem();
        addSeekBarItem();
        //
        this.addView(mTopLayout);
        this.addView(mBottomLayout);
        //
        Log.i(TAG, "Init view finished...");
    }


    /**
     * 添加 SeekBar
     */
    private void addSeekBarItem() {
        if (mTopLayout == null) {
            throw new RuntimeException("The loading-scale box should not be null!");
        }
        mTopLayout.removeAllViews();

        this.mSeekBar = (SeekBar_Ex) View.inflate(getContext(), R.layout.seekbar_ex, null);
        this.mSeekBar.setMax(mMax);

        int N = mMarkItemNum - 1;
        LayoutParams EmptyLP = new LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.WRAP_CONTENT);
        EmptyLP.gravity = Gravity.CENTER;
        EmptyLP.weight = N;//(float) (N - 1) / N
        LayoutParams SeekBarLP = new LayoutParams(ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.WRAP_CONTENT);
        SeekBarLP.gravity = Gravity.LEFT | Gravity.CENTER_VERTICAL;
        SeekBarLP.weight = 1;//(float) 1 / (2 * N)
        mSeekBar.setLayoutParams(SeekBarLP);
        TextView textViewLeft = new TextView(getContext());
        textViewLeft.setLayoutParams(EmptyLP);
        TextView textViewRight = new TextView(getContext());
        textViewRight.setLayoutParams(EmptyLP);

        //mTopLayout.setPadding(mSeekBarPaddingLen, 0, mSeekBarPaddingLen, 0);

        //mTopLayout.addView(textViewLeft);
        mTopLayout.addView(mSeekBar);
        mTopLayout.addView(textViewRight);
    }

    /**
     * 添加 刻度
     */
    private void addAllMarkItem() {
        if (mBottomLayout == null) {
            throw new RuntimeException("The loading-scale box should not be null!");
        }
        //
        mBottomLayout.removeAllViews();
        //
        Drawable drawablePoint = null;
        for (int i = 0; i < mMarkItemNum; i++) {

            LinearLayout markLL = (LinearLayout) View.inflate(getContext(), R.layout.dial, null);

            ImageView imageView = (ImageView) markLL.findViewById(R.id.markPoint);
            
            TextView textView = (TextView) markLL.findViewById(R.id.Value);

            //这个width只能设为 0dp，可不能设为 LayoutParams.WRAP_CONTENT ，否则它就会使得textView不都是一般大了，会影响刻度精准
            LayoutParams tvLp = new LayoutParams(LayoutParams.MATCH_PARENT, LayoutParams.WRAP_CONTENT);
            tvLp.weight = 1;
            tvLp.gravity = Gravity.LEFT | Gravity.CENTER_VERTICAL;//LayoutGravity.
            markLL.setLayoutParams(tvLp);


            textView.setGravity(Gravity.LEFT | Gravity.CENTER_VERTICAL);
            textView.setTextSize(mMarkTextSize);
            textView.setText(mMarkDescArray[i]);
            if (isShowPoint) {
                //
                if (mMarkDrawable != null) {
                    drawablePoint = mMarkDrawable;
                } else {
                    if (drawablePoint == null) {
                        drawablePoint = getContext().getResources().getDrawable(R.drawable.markpoint);
                    }
                }

                imageView.setImageDrawable(drawablePoint);
//                textView.setCompoundDrawablesWithIntrinsicBounds(null, drawablePoint, null, null);
                textView.setTextColor(mMarkTextColor);
//                textView.setCompoundDrawablePadding(
//                        (int) TypedValue.applyDimension(TypedValue.COMPLEX_UNIT_DIP,
//                                5,
//                                getResources().getDisplayMetrics()));
            }
            //
            mBottomLayout.addView(markLL);//textView
        }
    }

    public SeekBar getSeekBar() {
        return mSeekBar;
    }

    /**
     * 设置监听选中刻度
     */
    public void setOnSelectItemListener(OnSelectItemListener l) {
        this.mSelectItemListener = l;
    }

    public interface OnSelectItemListener {
        void selectItem(int nowSelectItemNum, String val);
    }

    @Override
    public void setEnabled(boolean enabled) {
        super.setEnabled(enabled);

        mSeekBar.setEnabled(enabled);
    }

    /**
     * 设置选中
     *
     * @param selectItemNum 0代表第一个刻度，1代表第二个刻度，以此类推
     */
    public void selectMarkItem(int selectItemNum) {
        //设置当前选中
        nowMarkItem = selectItemNum;
        //
        int shouldInProgress = mSpacing * selectItemNum;
        //
        mSeekBar.setProgress(shouldInProgress);
    }

    /**
     * 当前选中刻度
     *
     * @return
     */
    public int getNowMarkItem() {
        return nowMarkItem;
    }

    public int getProgress() {
        return mSeekBar.getProgress();
    }

    public void setProgress(int progress) {
        mSeekBar.setProgress(progress);
    }

    public static class ScreenUtils {

        private ScreenUtils() {
            throw new AssertionError();
        }

        public static float dpToPx(Context context, float dp) {
            if (context == null) {
                return -1;
            }
            return dp * context.getResources().getDisplayMetrics().density;
        }

        public static float pxToDp(Context context, float px) {
            if (context == null) {
                return -1;
            }
            return px / context.getResources().getDisplayMetrics().density;
        }

        public static int dpToPxInt(Context context, float dp) {
            return (int) (dpToPx(context, dp) + 0.5f);
        }

        public static int pxToDpCeilInt(Context context, float px) {
            return (int) (pxToDp(context, px) + 0.5f);
        }
    }

    @Override
    public boolean isInEditMode() {
        return true;
    }

    public void showHint() {
        if (mSeekBar.isShown())
            mSeekBar.showPopup();
    }

    public void hideHint() {
        if (mSeekBar.isShown())
            mSeekBar.hidePopup();
    }
}
