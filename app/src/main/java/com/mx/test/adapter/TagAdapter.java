package com.mx.test.adapter;

import android.view.View;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import com.mx.test.custom.FlowLayout;

public abstract class TagAdapter<T> {
	private List<T> mTagDatas;
	private OnDataChangedListener mOnDataChangedListener;
	private HashSet<Integer> mCheckedPosList = new HashSet<Integer>();

	public TagAdapter(List<T> datas) {
		mTagDatas = datas;
		// checkHashSet();
	}

	public TagAdapter(T[] datas) {
		mTagDatas = new ArrayList<T>(Arrays.asList(datas));
		// checkHashSet();
	}

	public interface OnDataChangedListener {
		void onChanged();
	}

	public void setOnDataChangedListener(OnDataChangedListener listener) {
		mOnDataChangedListener = listener;
	}

	// add ,为了解决评分碎片回到评价碎片HashSet为空的问题
	@SuppressWarnings("unused")
	private void checkHashSet() {

		if (mCheckedPosList == null) {
			mCheckedPosList = new HashSet<Integer>();
		}
	}

	public void setSelectedList(int... poses) {
		Set<Integer> set = new HashSet<>();
		for (int pos : poses) {
			set.add(pos);
		}
		setSelectedList(set);
	}

	public void setSelectedList(Set<Integer> set) {
		mCheckedPosList.clear();
		if (set != null)
			mCheckedPosList.addAll(set);
		notifyDataChanged();
	}

	public HashSet<Integer> getPreCheckedList() {
		return mCheckedPosList;
	}

	public int getCount() {
		return mTagDatas == null ? 0 : mTagDatas.size();
	}

	public void notifyDataChanged() {
		mOnDataChangedListener.onChanged();
	}

	public T getItem(int position) {
		return mTagDatas.get(position);
	}

	public abstract View getView(FlowLayout parent, int position, T t);

	public boolean setSelected(int position, T t) {
		return false;
	}

}