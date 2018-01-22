package com.mx.osce.adapter.croe;

import android.annotation.TargetApi;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;

/**
 * ListViewHolder
 * Created by mrsimple on 29/9/15.
 */
public class ListViewHolder {
    /**
     * ViewHolder实现类,桥接模式适配AbsListView与RecyclerView的二维变化
     */
    ViewHolderImpl mHolderImpl;

    private int mLayoutId;

    /**
     * @param itemView
     */
    ListViewHolder(View itemView, int layoutId) {
        mHolderImpl = new ViewHolderImpl(itemView);
        mLayoutId = layoutId;
    }

    public int getLayoutId() {
        return mLayoutId;
    }

    /**
     * @param viewId
     * @param <T>
     * @return
     */
    public <T extends View> T findViewById(int viewId) {
        return mHolderImpl.findViewById(viewId);
    }

    public Context getContext() {
        return mHolderImpl.mItemView.getContext();
    }

    /**
     * 获取GodViewHolder
     *
     * @param convertView
     * @param parent
     * @param layoutId
     * @return
     */
    public static ListViewHolder get(View convertView, ViewGroup parent, int layoutId) {
        ListViewHolder viewHolder;
        if (convertView == null) {
            convertView = LayoutInflater.from(parent.getContext()).inflate(layoutId, parent, false);
            viewHolder = new ListViewHolder(convertView, layoutId);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ListViewHolder) convertView.getTag();

            // 多种item类型时，需要判断复用的布局的layoutId是否等于当前的layoutId
            if (viewHolder.getLayoutId() != layoutId) {
                convertView = LayoutInflater.from(parent.getContext()).inflate(layoutId, parent, false);
                viewHolder = new ListViewHolder(convertView, layoutId);
                convertView.setTag(viewHolder);
            }
        }

        return viewHolder;
    }


    public View getItemView() {
        return mHolderImpl.getItemView();
    }

    public ListViewHolder setText(int viewId, int stringId) {
        mHolderImpl.setText(viewId, stringId);
        return this;
    }

    public ListViewHolder setText(int viewId, String text) {
        mHolderImpl.setText(viewId, text);
        return this;
    }

    public ListViewHolder setTextColor(int viewId, int color) {
        mHolderImpl.setTextColor(viewId, color);
        return this;
    }

    /**
     * @param viewId
     * @param color
     */
    public ListViewHolder setBackgroundColor(int viewId, int color) {
        mHolderImpl.setBackgroundColor(viewId, color);
        return this;
    }

    public ListViewHolder setBackgroundResource(int viewId, int resId) {
        mHolderImpl.setBackgroundResource(viewId, resId);
        return this;
    }

    public ListViewHolder setBackgroundDrawable(int viewId, Drawable drawable) {
        mHolderImpl.setBackgroundDrawable(viewId, drawable);
        return this;
    }

    @TargetApi(16)
    public ListViewHolder setBackground(int viewId, Drawable drawable) {
        mHolderImpl.setBackground(viewId, drawable);
        return this;
    }

    public ListViewHolder setImageBitmap(int viewId, Bitmap bitmap) {
        mHolderImpl.setImageBitmap(viewId, bitmap);
        return this;
    }

    public ListViewHolder setImageResource(int viewId, int resId) {
        mHolderImpl.setImageResource(viewId, resId);
        return this;
    }

    public ListViewHolder setImageDrawable(int viewId, Drawable drawable) {
        mHolderImpl.setImageDrawable(viewId, drawable);
        return this;
    }

    public ListViewHolder setImageDrawable(int viewId, Uri uri) {
        mHolderImpl.setImageDrawable(viewId, uri);
        return this;
    }

    @TargetApi(16)
    public ListViewHolder setImageAlpha(int viewId, int alpha) {
        mHolderImpl.setImageAlpha(viewId, alpha);
        return this;
    }

//    public ListViewHolder setChecked(int viewId, boolean checked) {
//        mHolderImpl.setChecked(viewId, checked);
//        return this;
//    }

    public ListViewHolder setProgress(int viewId, int progress) {
        mHolderImpl.setProgress(viewId, progress);
        return this;
    }

    public ListViewHolder setProgress(int viewId, int progress, int max) {
        mHolderImpl.setProgress(viewId, progress, max);
        return this;
    }

    public ListViewHolder setMax(int viewId, int max) {
        mHolderImpl.setMax(viewId, max);
        return this;
    }

    public ListViewHolder setRating(int viewId, float rating) {
        mHolderImpl.setRating(viewId, rating);
        return this;
    }

    public ListViewHolder setRating(int viewId, float rating, int max) {
        mHolderImpl.setRating(viewId, rating, max);
        return this;
    }

    public ListViewHolder setOnClickListener(int viewId, View.OnClickListener listener) {
        mHolderImpl.setOnClickListener(viewId, listener);
        return this;
    }

    public ListViewHolder setOnTouchListener(int viewId, View.OnTouchListener listener) {
        mHolderImpl.setOnTouchListener(viewId, listener);
        return this;
    }

    public ListViewHolder setOnLongClickListener(int viewId, View.OnLongClickListener listener) {
        mHolderImpl.setOnLongClickListener(viewId, listener);
        return this;
    }

    public ListViewHolder setOnItemClickListener(int viewId, AdapterView.OnItemClickListener listener) {
        mHolderImpl.setOnItemClickListener(viewId, listener);
        return this;
    }

    public ListViewHolder setOnItemLongClickListener(int viewId, AdapterView.OnItemLongClickListener listener) {
        mHolderImpl.setOnItemLongClickListener(viewId, listener);
        return this;
    }

    public ListViewHolder setOnItemSelectedClickListener(int viewId, AdapterView.OnItemSelectedListener listener) {
        mHolderImpl.setOnItemSelectedClickListener(viewId, listener);
        return this;
    }
}
