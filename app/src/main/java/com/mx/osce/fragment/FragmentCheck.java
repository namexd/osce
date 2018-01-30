package com.mx.osce.fragment;

import com.mx.osce.R;

import android.app.Fragment;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ExpandableListView;
import android.widget.TextView;

public class FragmentCheck extends Fragment {
	private TextView mTextStuName;
	private ExpandableListView mExpandScore;
@Override
public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {
	// TODO Auto-generated method stub
	View view  = inflater.inflate(R.layout.fragment_check, null);
	findView(view);
	return view;
}
private void findView(View view) {

	mTextStuName = (TextView) view.findViewById(R.id.text_name);
	mExpandScore = (ExpandableListView) view.findViewById(R.id.expand_datailScore);
}
}
