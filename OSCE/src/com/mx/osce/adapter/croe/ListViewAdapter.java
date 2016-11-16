package com.mx.osce.adapter.croe;

import java.util.ArrayList;
import java.util.List;

import com.mx.osce.adapter.croe.ListViewHolder;
import com.mx.osce.adapter.croe.MultiItemTypeSupport;

import android.util.Log;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;

/**
 * ListViewAdapter
 * Created by mrsimple on 25/9/15.
 */
public abstract class ListViewAdapter<D> extends BaseAdapter {

    /**
     * 数据集
     */
    protected final List<D> mDataSet = new ArrayList<D>();
    /**
     * Item Layout
     */
    private int mItemLayoutId;

    private MultiItemTypeSupport<D> mMultiItemSupport;

    /**
     * 单条目类型使用此构造方法
     */
    public ListViewAdapter(int layoutId, List<D> datas) {
        mItemLayoutId = layoutId;
        mDataSet.addAll(datas);
    }

    /**
     * 多种条目类型时需要传入一个支持接口
     */
    public ListViewAdapter(MultiItemTypeSupport<D> support, List<D> datas) {
        this.mMultiItemSupport = support;
        mDataSet.addAll(datas);
    }

    @Override
    public int getViewTypeCount() {
        return mMultiItemSupport == null ?
                super.getViewTypeCount() : mMultiItemSupport.getViewTypeCount();
    }

    @Override
    public int getItemViewType(int position) {
        return mMultiItemSupport == null ?
                super.getItemViewType(position) :
                mMultiItemSupport.getItemViewType(position, mDataSet.get(position));
    }

    /**
     * 根据View Type返回布局资源
     */
    public int getItemLayout(int type) {
        return mMultiItemSupport == null ? mItemLayoutId : mMultiItemSupport.getItemLayout(type);
    }

    /**
     * @param item
     */
    public void addItem(D item) {
        mDataSet.add(item);
        notifyDataSetChanged();
    }

    /**
     * @param items
     */
    public void addItems(List<D> items) {
        mDataSet.addAll(items);
        notifyDataSetChanged();
    }

    /**
     * @param item
     */
    public void addItemToHead(D item) {
        mDataSet.add(0, item);
        notifyDataSetChanged();
    }

    /**
     * @param items
     */
    public void addItemsToHead(List<D> items) {
        mDataSet.addAll(0, items);
        notifyDataSetChanged();
    }

    /**
     * @param position
     */
    public void remove(int position) {
        mDataSet.remove(position);
        notifyDataSetChanged();
    }

    /**
     * @param item
     */
    public void remove(D item) {
        mDataSet.remove(item);
        notifyDataSetChanged();
    }

    public void clear() {
        mDataSet.clear();
        notifyDataSetChanged();
    }

    /**
     * @return
     */
    @Override
    public int getCount() {
        Log.e("dashixiong3", ""+mDataSet.size());
        return mDataSet.size();
    }

    /**
     * @param position
     * @return
     */
    @Override
    public D getItem(int position) {
        Log.e("dashixiong4", ""+position);
        return mDataSet.get(position);
    }

    /**
     * @param position
     * @return
     */
    @Override
    public long getItemId(int position) {
        return position;
    }

    /**
     * 封装getView逻辑,将根据viewType获取布局资源、解析布局资源、创建ViewHolder等逻辑封装起来,简化使用流程
     *
     * @param position
     * @param convertView
     * @param parent
     * @return
     */
    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        int layoutId = getItemLayout(getItemViewType(position));
        ListViewHolder viewHolder = ListViewHolder.get(convertView, parent, layoutId);
        // 绑定数据
        Log.e("dashixiong1", ""+position);
        Log.e("dashixiong2", ""+getItem(position));
        onBindData(viewHolder, position, getItem(position));
        return viewHolder.getItemView();
    }

    /**
     * 绑定数据到Item View上
     *
     * @param viewHolder
     * @param position   数据的位置
     * @param item       数据项
     */
    protected abstract void onBindData(ListViewHolder viewHolder, int position, D item);

}
