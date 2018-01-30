package com.mx.test.fragment;

import com.mx.test.GradeActivity;
import com.mx.test.R;

import android.app.Fragment;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

public class FragmentDrawSuccess extends Fragment {
	private static String TAG = "FragmentDrawSuccess";
	private ImageView mImagePhoto;
	// 姓名,学号,身份证,考号,异常原因
	private TextView mName, mCode, mCardId, mTestCode;
	// private TextView mbeginTest, mWarn;
	private TextView mbeginTest;
	private Context mContext;

	@Override
	public View onCreateView(LayoutInflater inflater, ViewGroup container, Bundle savedInstanceState) {

		View view = inflater.inflate(R.layout.fragment_draw_success, null);
		mContext = getActivity();
		findWidget(view);
		return view;
	}

	private void findWidget(View view) {
		mImagePhoto = (ImageView) view.findViewById(R.id.image_student_photo);
		mName = (TextView) view.findViewById(R.id.tv_student_name);
		mCode = (TextView) view.findViewById(R.id.tv_studentId);
		mCardId = (TextView) view.findViewById(R.id.tv_idCard);
		mTestCode = (TextView) view.findViewById(R.id.tv_text_Num);
		mbeginTest = (TextView) view.findViewById(R.id.textview_begin);
		mImagePhoto.setImageResource(R.drawable.pic);
		mName.setText(getArguments().getString("name"));
		mCode.setText(getArguments().getString("code"));
		mCardId.setText(getArguments().getString("sfz"));
		mTestCode.setText(getArguments().getString("zkz"));
		mbeginTest.setOnClickListener(new View.OnClickListener() {
			@Override
			public void onClick(View view) {
				Intent intent = new Intent(getActivity(), GradeActivity.class);
				startActivity(intent);
				getActivity().finish();
			}
		});
	}

}
