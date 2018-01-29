//
//  locationController.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/17/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import Foundation
import UIKit
import Alamofire

class locationController: UIViewController,
UINavigationControllerDelegate, UITableViewDataSource, UITableViewDelegate, UISearchBarDelegate, UIGestureRecognizerDelegate, UITextFieldDelegate {
	
	var useFilteredData = false
	var buildingFilter = false
	var departmentFilter = false
	
	@IBOutlet var backgroundView: UIView!
	
	@IBOutlet weak var roomNumField: UITextField!
	
	@IBOutlet weak var buildingSearch: UISearchBar!
	@IBOutlet weak var buildingTable: UITableView!
	var filteredCampusBuildings = [String]()
	
	@IBOutlet weak var departmentSearch: UISearchBar!
	@IBOutlet weak var departmentTable: UITableView!
	var filteredDepartNames = [String]()
	
	@IBOutlet weak var datePicker: UIDatePicker!
	var backgroundTouched: UITapGestureRecognizer!
	
	
	@IBOutlet var roomNumberTopConstraint: NSLayoutConstraint!
	@IBOutlet var scrollView: UIScrollView!
	@IBOutlet var buildingTopConstraint: NSLayoutConstraint!
	@IBOutlet var contentView: UIView!
	@IBOutlet var contentViewHeightConstraint: NSLayoutConstraint!
	var largeContentSize: Bool = false
	var dataForNextView: Int = 0
	var loadingIndicator: UIActivityIndicatorView = UIActivityIndicatorView(activityIndicatorStyle: .whiteLarge)
	var timer = Timer()
	override func viewDidAppear(_ animated: Bool) {
		super.viewDidAppear(animated)
		// Keyboard stuff.
		let center: NotificationCenter = NotificationCenter.default
		center.addObserver(self, selector: #selector(locationController.keyboardWillShow), name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		center.addObserver(self, selector: #selector(locationController.keyboardWillHide), name: NSNotification.Name.UIKeyboardWillHide, object: nil)
	}
	
	override func viewDidLoad() {
		super.viewDidLoad()
		self.navigationItem.title = ""
		
		increaseContentViewSize(largeView: false)
		
		buildingSearch.delegate = self
		departmentSearch.delegate = self
		
		buildingTable.delegate = self
		buildingTable.dataSource = self
		departmentTable.delegate = self
		departmentTable.dataSource = self
		
		self.buildingTable.register(UITableViewCell.self, forCellReuseIdentifier: "cell")
		self.departmentTable.register(UITableViewCell.self, forCellReuseIdentifier: "cell2")
		
		buildingTable.isHidden = true
		departmentTable.isHidden = true
		
		roomNumField.delegate = self
		
		// Keyboard stuff.
//		let center: NSNotificationCenter = NSNotificationCenter.defaultCenter()
//		center.addObserver(self, selector: #selector(locationController.keyboardWillShow(_:)), name: UIKeyboardWillShowNotification, object: nil)
//		center.addObserver(self, selector: #selector(locationController.keyboardWillHide(_:)), name: UIKeyboardWillHideNotification, object: nil)
//		
		useFilteredData = false
		buildingFilter = false
		departmentFilter = false
		
		backgroundTouched = UITapGestureRecognizer(target: self, action: #selector(locationController.hideKeyboardAndDismissTableViews))
		backgroundTouched.delegate = self
		view.addGestureRecognizer(backgroundTouched!)
		NotificationCenter.default.addObserver(self, selector: #selector(locationController.personKindSuccess), name: NSNotification.Name(rawValue: "personKindSuccess"), object: nil)
		timer = Timer.scheduledTimer(timeInterval: 2, target: self, selector: #selector(locationController.flash), userInfo: nil, repeats: true)
		timer.fire()
		self.datePicker.maximumDate = Date()
		let currentCalendar = NSCalendar.current
		let dateComponents = NSDateComponents()
		dateComponents.month = -1
		//dateComponents.year = -1
		//let oneYearBack = currentCalendar.date(byAdding: dateComponents as DateComponents, to: Date())!
		let oneMonthBack = currentCalendar.date(byAdding: dateComponents as DateComponents, to: Date())!
		self.datePicker.minimumDate = oneMonthBack
	}
	func flash()
	{
		//print("Flash scroll bar")
		scrollView.flashScrollIndicators()
	}
	
	func personKindSuccess(notification: NSNotification)
	{
		dataForNextView = dataForNextView + 1
		if(dataForNextView == 1)
		{
			dismissLoadingView()
		}
	}
	
	
	func dismissLoadingView()
	{
		loadingIndicator.isHidden = true
		loadingIndicator.stopAnimating()
	}
	func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive touch: UITouch) -> Bool {
		
		if gestureRecognizer == backgroundTouched
		{
			if (touch.view!.isDescendant(of: buildingTable) || touch.view!.isDescendant(of: departmentTable))
			{
				//print("Neglect ui view touch")
				return false
			}
			if animating
			{
				// dont allow background touches while view is moving
				return false
			}
			//print("Allow touch from background")
		}
		//	print("Allow gesture")
		return true
	}
	func hideKeyboardAndDismissTableViews(sender: UITapGestureRecognizer)
	{
		self.view.endEditing(true)
		if buildingTable.isHidden == false
		{
			buildingTable.isHidden = true
		}
		if departmentTable.isHidden == false{
			departmentTable.isHidden = true
		}
		self.contentViewHeightConstraint.constant = 576
		
	}
	func searchBarSearchButtonClicked(_ searchBar: UISearchBar) {
		self.view.endEditing(true);
	}
	override func didReceiveMemoryWarning() {
		super.didReceiveMemoryWarning()
		// Dispose of any resources that can be recreated.
	}
	
	override func viewWillDisappear(_ animated: Bool) {
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillShow, object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillHide, object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: "personKindSuccess") , object: nil)
		//print("Stop timer")
		timer.invalidate()
		self.view.endEditing(true)
	}
	
	func textFieldShouldReturn(_ textField: UITextField) -> Bool {
		self.view.endEditing(true)
		return false
	}
	
	var hasMoved = false
	var keyboardOffset: CGFloat = 0
	
	weak var activeField: UITextField?
	weak var activeSearch: UISearchBar?
	func textFieldDidEndEditing(_ textField: UITextField) {
		self.activeField = nil
	}
	
	func textFieldDidBeginEditing(_ textField: UITextField) {
		self.activeField = textField
	}
	func keyboardWillBeHidden(notification: NSNotification) {
		let contentInsets = UIEdgeInsets.zero
		self.scrollView.contentInset = contentInsets
		self.scrollView.scrollIndicatorInsets = contentInsets
	}
	func keyboardWillShow(notification: NSNotification) {
		let info:NSDictionary = notification.userInfo! as NSDictionary
		let keyboardSize = (info[UIKeyboardFrameBeginUserInfoKey] as! NSValue).cgRectValue
		
		let contentInsets = UIEdgeInsets(top: 0.0, left: 0.0, bottom: keyboardSize.height, right: 0.0)
		self.scrollView.contentInset = contentInsets
		self.scrollView.scrollIndicatorInsets = contentInsets
		var aRect = self.view.frame
		aRect.size.height -= keyboardSize.size.height
		if(activeField != nil)
		{
			if (!aRect.contains(activeField!.frame.origin)) {
				self.scrollView.scrollRectToVisible(activeField!.frame, animated: true)
			}
		}
		if(activeSearch != nil)
		{
			if (!aRect.contains(activeSearch!.frame.origin)) {
				self.scrollView.scrollRectToVisible(activeSearch!.frame, animated: true)
			}
		}
		//print("show")
	}
	
	func keyboardWillHide(notification: NSNotification) {

		keyboardOffset = 0
		//print("hide")
	}
	
	
	func filterContentForSearchText(searchText: String) {
		//let search = searchText.lowercased()
		if buildingTable.isHidden == false {
			//print("filter building")
			filteredCampusBuildings = preloadedData.buildingNames.filter() { $0.lowercased().hasPrefix(searchText.lowercased()) }
		} else {
			//print("filter departments")
			filteredDepartNames = preloadedData.departmentNames.filter() { $0.lowercased().hasPrefix(searchText.lowercased()) }
		}
	}
	func increaseContentViewSize(largeView: Bool)
	{
		if(largeView){
			if(!largeContentSize){
			scrollView.contentSize = CGSize(width: self.view.bounds.width, height: 801)
				largeContentSize = true
			}
		}
		else{
			if(largeContentSize){
				scrollView.contentSize = CGSize(width: self.view.bounds.width, height: 500)
				largeContentSize = false
			}
			
		}
	}
	var animating: Bool = false
	func searchBarShouldBeginEditing(_ searchBar: UISearchBar) -> Bool {
	
		self.activeSearch = searchBar
		//print("Begin editing")
		if searchBar == buildingSearch {
			//print("Building")
			
			self.roomNumberTopConstraint.constant = 239
			//self.contentViewHeightConstraint.constant = 801
			self.increaseContentViewSize(largeView: true)
			animating = true
			UIView.animate(withDuration: 0.3,
			                           delay: 0.0,
			                           options: UIViewAnimationOptions.curveEaseIn,
			                           animations: {
										self.contentView.layoutIfNeeded()
										
				},
			                           completion: { finished in
										self.departmentTable.isHidden = true
										self.buildingTable.isHidden = false
										//datePicker.hidden = true
										self.buildingTable.reloadData()
										self.animating = false
										
			})

		} else {
			
			self.buildingTopConstraint.constant = 239 
			//self.contentViewHeightConstraint.constant = 801
			self.increaseContentViewSize(largeView: true)
			animating = true
			UIView.animate(withDuration: 0.3,
			                           delay: 0.0,
			                           options: UIViewAnimationOptions.curveEaseIn,
			                           animations: {
										self.contentView.layoutIfNeeded()
										
				},
			                           completion: { finished in
										//print("Department")
										self.buildingTable.isHidden = true
										self.departmentTable.isHidden = false
										//roomNumField.hidden = true
										self.departmentTable.reloadData()
										self.animating = false
			})

		}
		return true
	
	}
	
	func searchBarShouldEndEditing(_ searchBar: UISearchBar) -> Bool {
		
		self.activeSearch = nil
		if searchBar == buildingSearch {
			buildingTable.isHidden = true
			//datePicker.hidden = false
			self.roomNumberTopConstraint.constant = 14
			//self.contentViewHeightConstraint.constant = 576
			increaseContentViewSize(largeView: false)
			animating = true
			UIView.animate(withDuration: 0.3,
			                           delay: 0.0,
			                           options: UIViewAnimationOptions.curveEaseIn,
			                           animations: {
										self.contentView.layoutIfNeeded()
										
				},
			                           completion: { finished in
										self.animating = false
										
			})

		} else {
			departmentTable.isHidden = true
			//roomNumField.hidden = false
			self.buildingTopConstraint.constant = 14
			//self.contentViewHeightConstraint.constant = 576
			self.increaseContentViewSize(largeView: false)
			animating = true
			UIView.animate(withDuration: 0.3,
			                           delay: 0.0,
			                           options: UIViewAnimationOptions.curveEaseIn,
			                           animations: {
										self.contentView.layoutIfNeeded()
										
				},
			                           completion: { finished in
										self.animating = false
										
			})

		}
		return true
	
	}
	
	func stateChanged(switchState: UISwitch) {
		buildingTable.reloadData()
	}
	
	func searchBar(_ searchBar: UISearchBar, textDidChange searchText: String) {
		if(searchBar == buildingSearch) {
			departmentTable.isHidden = true
			buildingTable.isHidden = false
			if searchBar.text?.characters.count != 0 {
				//print("Text in the search bar - building [\(searchBar.text)]")
				useFilteredData = true
				buildingFilter = true
				filterContentForSearchText(searchText: buildingSearch.text!)
			} else {
				//print("Empty search bar building")
				useFilteredData = false
				buildingFilter = false
			}
			buildingTable.reloadData()
		} else {
			buildingTable.isHidden = true
			departmentTable.isHidden = false
			if searchBar.text?.characters.count != 0 {
				//print("Text in the search bar - department [\(searchBar.text)]")
				useFilteredData = true
				departmentFilter = true
				filterContentForSearchText(searchText: departmentSearch.text!)
			} else {
				//	print("Empty search bar department")

				useFilteredData = false
				departmentFilter = false
			}
			departmentTable.reloadData()
		}
	}
	
	func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
		if tableView == buildingTable {
			if buildingFilter {
				return filteredCampusBuildings.count
			} else {
				return preloadedData.buildingNames.count
			}
		} else {
			if departmentFilter {
				return filteredDepartNames.count
			} else {
				return preloadedData.departmentNames.count
			}
		}
	}
	
	func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
		if tableView == buildingTable {
			let cell:UITableViewCell = buildingTable.dequeueReusableCell(withIdentifier: "cell")! as UITableViewCell
			if buildingFilter {
				cell.textLabel?.text = filteredCampusBuildings[indexPath.row]
			} else {
				cell.textLabel?.text = preloadedData.buildingNames[indexPath.row]
			}
			cell.textLabel?.numberOfLines = 2
		
			return cell
		} else {
			let cell2:UITableViewCell = departmentTable.dequeueReusableCell(withIdentifier: "cell2")! as UITableViewCell
			if departmentFilter {
				cell2.textLabel?.text = filteredDepartNames[indexPath.row]
			} else {
				cell2.textLabel?.text = preloadedData.departmentNames[indexPath.row]
			}
			cell2.textLabel?.numberOfLines = 2
			return cell2
		}
	}
	
	func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
		if tableView == buildingTable {
			if buildingFilter {
				buildingSearch.text = filteredCampusBuildings[indexPath.row]
			} else {
				buildingSearch.text = preloadedData.buildingNames[indexPath.row]
			}
			buildingFilter = false
			buildingTable.isHidden = true
			self.view.endEditing(true)
		} else {
			if departmentFilter {
				departmentSearch.text = filteredDepartNames[indexPath.row]
			} else {
				departmentSearch.text = preloadedData.departmentNames[indexPath.row]
			}
			departmentFilter = false
			departmentTable.isHidden = true
			self.view.endEditing(true)
		}
	}
	
	@IBAction func nextButtonPressed(sender: AnyObject) {
		finalReportData.departmentName = departmentSearch.text!
		finalReportData.buildingName = buildingSearch.text!
		finalReportData.roomNum = roomNumField.text!
		
		//		let formatter: NSDateFormatter = NSDateFormatter()
		//		formatter.dateStyle = NSDateFormatter.dateFormatFromTemplate("yyyy-MM-dd", options: 0, locale: NSLocale(localeIdentifier: "en-US"))
		//		formatter.timeStyle = NSDateFormatter.dateFormatFromTemplate("hh:mm:ss", options: 0, locale: NSLocale(localeIdentifier: "en-US"))
		//		formatter.timeZone = NSTimeZone.defaultTimeZone()
		
		let dateFormatter = DateFormatter()
		dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
		//dateFormatter.timeStyle = "HH:mm:ss"
		dateFormatter.timeZone = NSTimeZone.default
		finalReportData.time = dateFormatter.string(from: datePicker.date)
	}
	
	//---------------- VALIDATION ----------------
	//determine whether to block segue or not
	override func shouldPerformSegue(withIdentifier identifier: String, sender: Any?) -> Bool {
		if ((buildingSearch.text == "")
			|| (departmentSearch.text == "")
			){
			let alertController = UIAlertController(title: "Invalid Input", message: "Please select a Building and Department", preferredStyle: .alert)
			
			let OKAction = UIAlertAction(title: "OK", style: .default) { (action) in }
			alertController.addAction(OKAction)
			
			self.present(alertController, animated: true) {}
			
			return false
		}
		else if (!preloadedData.buildingNames.contains(buildingSearch.text!) || !preloadedData.departmentNames.contains(departmentSearch.text!))
		{
			let alertController = UIAlertController(title: "Invalid Input", message: "Please select a valid Building and Department", preferredStyle: .alert)
			
			let OKAction = UIAlertAction(title: "OK", style: .default) { (action) in }
			alertController.addAction(OKAction)
			
			self.present(alertController, animated: true) {}
			return false
		}
		if(roomNumField.text == "" || (roomNumField.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!)
		{
			let alertController = UIAlertController(title: "Invalid Input", message: "Please input a room number or room title such as lobby", preferredStyle: .alert)
			
			let OKAction = UIAlertAction(title: "OK", style: .default) { (action) in }
			alertController.addAction(OKAction)
			
			self.present(alertController, animated: true) {}
			return false
		}
		if identifier == "personalView"
		{
			if(preloadedData.personKinds.count == 0)
			{
				let alert: UIAlertController = UIAlertController(title: "Please wait...", message: "\n\n\n\nData connection required.\n Please connect to a stronger connection before trying again.", preferredStyle: .alert)
				//self.loadingIndicator = UIActivityIndicatorView(frame: CGRectMake(50, 10, 37, 37))
				self.loadingIndicator.frame = CGRect(x: 110.5, y: 50, width: 50, height: 50)
				self.loadingIndicator.activityIndicatorViewStyle = .whiteLarge
				self.loadingIndicator.color = UIColor.black
				//  loadingIndicator.center = view.center;
				self.loadingIndicator.hidesWhenStopped = true
				self.loadingIndicator.startAnimating()
				alert.view.addSubview(self.loadingIndicator)
				print("Accessing php scripts")
				//setPersonalView()
				preload()
				alert.addAction(UIAlertAction(title: "Ok", style: .default, handler: {
					action in
					
					
					self.dismiss(animated: true, completion: nil)
				}))
				self.present(alert, animated: true, completion: nil)
				return false
			}
		}
		
		let dateFormatter = DateFormatter()
		dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
		//dateFormatter.timeStyle = "HH:mm:ss"
		dateFormatter.timeZone = NSTimeZone.default
		//let deliveryTime = NSDateFormatter.localizedStringFromDate(datePicker.date, dateStyle: dateFormatter.dateStyle, timeStyle: dateFormatter.timeStyle)
		
		print("The Time is \(dateFormatter.string(from: datePicker.date))")
		
		// by default, transition
		return true
		

	}
	
	
}
