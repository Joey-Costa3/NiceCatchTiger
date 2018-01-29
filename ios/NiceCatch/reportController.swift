//
//  reportController.swift
//  NiceCatch
//
//  Created by Joey Costa on 11/11/15.
//  Copyright © 2015 Joey Costa. All rights reserved.

import Foundation
import UIKit
import CoreData
import Alamofire

class reportController: UIViewController, UIPickerViewDataSource, UIPickerViewDelegate, UITextFieldDelegate {
	
	
	@IBOutlet var describeTopConstraint: NSLayoutConstraint!// 42
	@IBOutlet var involveTopConstriaint: NSLayoutConstraint! // 19
	@IBOutlet var infoTopConstraint: NSLayoutConstraint!
	
	
	
    @IBOutlet weak var reportPicker: UIPickerView!
    @IBOutlet weak var involvePicker: UIPickerView!
    
    //these are hidden "other" text fields
    @IBOutlet weak var reportTextBox: UITextField!
    @IBOutlet weak var involveTextBox: UITextField!
    
    @IBOutlet weak var incidentView: UITextView!
    
	@IBOutlet var scrollView: UIScrollView!
	
	@IBOutlet var contentView: UIView!
	var dataForNextView: Int = 0
	var timer = Timer()
	var loadingIndicator: UIActivityIndicatorView = UIActivityIndicatorView(activityIndicatorStyle: .whiteLarge)
	
    //------------------------ UI Methods ------------------default    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)
        // Keyboard stuff.
        let center: NotificationCenter = NotificationCenter.default
        center.addObserver(self, selector: #selector(personalViewController.keyboardWillShow), name: NSNotification.Name.UIKeyboardWillShow, object: nil)
        center.addObserver(self, selector: #selector(personalViewController.keyboardWillHide), name: NSNotification.Name.UIKeyboardWillHide, object: nil)
		timer = Timer.scheduledTimer(timeInterval: 3, target: self, selector: #selector(reportController.flash), userInfo: nil, repeats: true)
		timer.fire()
    }
    func flash()
	{
		scrollView.flashScrollIndicators()
	}
	
    override func viewDidLoad() {
        super.viewDidLoad()
	
		incidentView.layer.cornerRadius = 5
		self.navigationItem.title = ""
        reportPicker.dataSource = self
        reportPicker.delegate = self
        involvePicker.dataSource = self
        involvePicker.delegate = self
        reportTextBox.delegate = self
        involveTextBox.delegate = self
        
        //these are hidden "other" text fields
        reportTextBox.isHidden = true;
        involveTextBox.isHidden = true;
        
        // Keyboard hiding
//        let center: NSNotificationCenter = NSNotificationCenter.defaultCenter()
//        center.addObserver(self, selector: #selector(reportController.keyboardWillShow(_:)), name: UIKeyboardWillShowNotification, object: nil)
//        center.addObserver(self, selector: #selector(reportController.keyboardWillHide(_:)), name: UIKeyboardWillHideNotification, object: nil)
        let tap: UITapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(reportController.dismissKeyboard))
        view.addGestureRecognizer(tap)
        
//        //not loaded. load manually and refresh
//        if(preloadedData.reportKinds.count == 0){
//            self.loadReportKinds()
//        }
//        
//        //not loaded. load manually and refresh
//        if(preloadedData.involvementKinds.count == 0){
//            self.loadInvolvementKinds()
//        }
		NotificationCenter.default.addObserver(self, selector: #selector(reportController.buildingsSuccess), name: NSNotification.Name(rawValue: "buildingsSuccess"), object: nil)
		NotificationCenter.default.addObserver(self, selector: #selector(reportController.departmentsSuccess), name: NSNotification.Name(rawValue: "departmentsSuccess"), object: nil)
		navigationController?.navigationItem.backBarButtonItem?.title = "Back"
    }//end viewDidLoad
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
	func buildingsSuccess(notification: NSNotification)
	{
		dataForNextView = dataForNextView + 1
		if(dataForNextView == 2)
		{
			dismissLoadingView()
		}
	}
	func departmentsSuccess(notification: NSNotification)
	{
		dataForNextView = dataForNextView + 1
		if (dataForNextView == 2)
		{
			dismissLoadingView()
		}
	}
	
	func dismissLoadingView()
	{
		loadingIndicator.isHidden = true
		loadingIndicator.stopAnimating()
	}



    //------------------------ KEYBOARD METHODS ------------------------
    
    func dismissKeyboard() {
        //Causes the view (default embedded text fields) to resign the first responder status.
        self.view.endEditing(true)
	}

	
	override func viewWillDisappear(_ animated: Bool) {
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillShow, object: nil)
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name.UIKeyboardWillHide, object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: "departmentsSuccess") , object: nil)
		NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: "buildingsSuccess") , object: nil)
		//print("Stop timer")
		timer.invalidate()
    }
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        self.view.endEditing(true)
        return false
    }

    var hasMoved = false
    
    //begin editing
    func keyboardWillShow(notification: NSNotification) {
        let info:NSDictionary = notification.userInfo! as NSDictionary
        let keyboardSize = (info[UIKeyboardFrameBeginUserInfoKey] as! NSValue).cgRectValue
        
        let keyboardHeight: CGFloat = keyboardSize.height
        
        if !hasMoved && incidentView.isFirstResponder {
			UIView.animate(withDuration: 0.05, animations: { () -> Void in
				self.view.center.y = self.view.center.y - keyboardHeight
				
				}, completion: { (complete: Bool) in
					self.hasMoved = true
					return
			})
			// hasMoved = true
        }
		//print("show")

    }
	
    //done editing
    func keyboardWillHide(notification: NSNotification) {
        let info: NSDictionary = notification.userInfo! as NSDictionary
        let keyboardSize = (info[UIKeyboardFrameBeginUserInfoKey] as! NSValue).cgRectValue
        
        let keyboardHeight: CGFloat = keyboardSize.height
        
        if hasMoved && incidentView.isFirstResponder {
			UIView.animate(withDuration: 0.05, animations: { () -> Void in
				self.view.center.y = self.view.center.y + keyboardHeight
				
				}, completion: { (complete: Bool) in
					self.hasMoved = false
			return
			})
			//hasMoved = false
        }
//		let userInfo: [NSObject : AnyObject] = notification.userInfo!
//		
//		let keyboardSize: CGSize = userInfo[UIKeyboardFrameBeginUserInfoKey]!.CGRectValue.size
//		let offset: CGSize = userInfo[UIKeyboardFrameEndUserInfoKey]!.CGRectValue.size
//		
//		if keyboardSize.height == offset.height {
//			if self.view.frame.origin.y == 0 {
//				UIView.animateWithDuration(0.1, animations: { () -> Void in
//					self.view.frame.origin.y += keyboardSize.height
//				})
//			}
//		} else {
//			UIView.animateWithDuration(0.1, animations: { () -> Void in
//				self.view.frame.origin.y -= keyboardSize.height - offset.height
//			})
//		}
		//print("hide")
    }
    //------------------------ ACTION HANDLERS ------------------------
    
    @IBAction func reportInfoPressed(sender: AnyObject) {
        let alert = UIAlertController(title: "Report Defintions", message: "Close Call - A situation that could have led to an injury or property damage, but did not.\n\nLesson Learned – Knowledge gained from a positive or negative experience.\n\nSafety Issue – Any action observed or participated in that can lead to injury or property damage.", preferredStyle: UIAlertControllerStyle.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: nil))
        self.present(alert, animated: true, completion: nil)
    }
    
    @IBAction func involveInfoPressed(sender: AnyObject) {
        let alert = UIAlertController(title: "Involvement Defintions", message: "Work Practice/Procedure – Examples include the use of outdated procedures and missing steps to complete the procedure/process safely and successfully.\n\nChemical – Examples include chemical spills and the use of improper Personal Protective Equipment while handling chemicals.\n\nEquipment – Examples include faulty equipment or the use of the wrong equipment for the task.\n\nWorkplace Condition – Examples include poor housekeeping (clutter), skipping/tripping hazards, and limited workspace to safely complete the task.", preferredStyle: UIAlertControllerStyle.alert)
        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: nil))
        self.present(alert, animated: true, completion: nil)
    }
    
    @IBAction func saveInfoPress(sender: AnyObject) {
        if reportSelection == "Other" {
            finalReportData.reportKind = reportTextBox.text!
        } else {
            if reportSelection == "" {
                reportSelection = preloadedData.reportKinds[0]
            }
            finalReportData.reportKind = reportSelection
        }
        if involveSelection == "Other" {
            finalReportData.involveKind = involveTextBox.text!
        } else {
            if involveSelection == "" {
                involveSelection = preloadedData.involvementKinds[0]
            }
            finalReportData.involveKind = involveSelection
        }
        finalReportData.incidentDesc = incidentView.text!
        
        self.view.endEditing(true)
    }
    
    //------------------------ PICKER METHODS ------------------------
    
    //how many pickers are in the view (always 1 in this case)
	
	func numberOfComponents(in pickerView: UIPickerView) -> Int {
		return 1
	}
    //determines how many rows of data are in the picker
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        if pickerView == reportPicker {
            return preloadedData.reportKinds.count
        } else {
            return preloadedData.involvementKinds.count
        }
    }
    
    //populates picker, depending on which picker view calls the method
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
        if pickerView == reportPicker {
            return preloadedData.reportKinds[row]
        } else {
            return preloadedData.involvementKinds[row]
        }
    }
	
	func performAnimationOnSelector(object: AnyObject, add: Bool)
	{
		UIView.animate(withDuration: 0.25,
		                           delay: 0.0,
		                           options: UIViewAnimationOptions.curveEaseIn,
		                           animations: {
									switch (add){
									case true:
										(object as! NSLayoutConstraint).constant += 42
									case false:
										(object as! NSLayoutConstraint).constant -= 42
									}
			},
		                           completion: { finished in
		})
	}
    //identifies what item the user chose
    var reportSelection: String = ""
    var involveSelection: String = ""
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        if pickerView == reportPicker {
            if preloadedData.reportKinds[row] == "Other" {
				involveTopConstriaint.constant = 19
				infoTopConstraint.constant = 14
				UIView.animate(withDuration: 0.3,
				                           delay: 0.0,
				                           options: UIViewAnimationOptions.curveEaseIn,
				                           animations: {
									self.contentView.layoutIfNeeded()

					},
				                           completion: { finished in
								                self.reportTextBox.isHidden = false
			
				})
            } else {
//                reportTextBox.hidden = true
				self.reportTextBox.isHidden = true
				involveTopConstriaint.constant = -23
				infoTopConstraint.constant = -28
				UIView.animate(withDuration: 0.3,
				                           delay: 0.0,
				                           options: UIViewAnimationOptions.curveEaseIn,
				                           animations: {
											self.contentView.layoutIfNeeded()
											
					},
				                           completion: { finished in
											
				})
            }
            reportSelection = preloadedData.reportKinds[row]
        } else {
            if preloadedData.involvementKinds[row] == "Other" {
				//  involveTextBox.hidden = false
				describeTopConstraint.constant = 19
				UIView.animate(withDuration: 0.3,
				                           delay: 0.0,
				                           options: UIViewAnimationOptions.curveEaseIn,
				                           animations: {
											self.contentView.layoutIfNeeded()
											
					},
				                           completion: { finished in
											self.involveTextBox.isHidden = false
											
				})
            } else {
//                involveTextBox.hidden = true
				self.involveTextBox.isHidden = true
				describeTopConstraint.constant = -23
				UIView.animate(withDuration: 0.3,
				                           delay: 0.0,
				                           options: UIViewAnimationOptions.curveEaseIn,
				                           animations: {
											self.contentView.layoutIfNeeded()
											
					},
				                           completion: { finished in
											
				})
            }
            involveSelection = preloadedData.involvementKinds[row]
        }
		self.view.endEditing(true)
    }
    
    func pickerView(_ pickerView: UIPickerView, viewForRow row: Int, forComponent component: Int, reusing view: UIView?) -> UIView {
        let pickerLabel = UILabel()
        var titleData:String
        if pickerView == reportPicker {
            titleData = preloadedData.reportKinds[row]
        } else {
            titleData = preloadedData.involvementKinds[row]
        }
        if (self.view.traitCollection.horizontalSizeClass == UIUserInterfaceSizeClass.regular) {
            let myTitle = NSAttributedString(string: titleData, attributes: [NSFontAttributeName:UIFont(name: "Helvetica", size: 36.0)!,NSForegroundColorAttributeName:UIColor.black])
            pickerLabel.attributedText = myTitle
            pickerLabel.textAlignment = .center
            return pickerLabel
        } else {
            let myTitle = NSAttributedString(string: titleData, attributes: [NSFontAttributeName:UIFont(name: "Helvetica", size: 24.0)!,NSForegroundColorAttributeName:UIColor.black])
            pickerLabel.attributedText = myTitle
            pickerLabel.textAlignment = .center
            return pickerLabel
        }
    }
	
    func pickerView(_ pickerView: UIPickerView, rowHeightForComponent component: Int) -> CGFloat {
        if (self.view.traitCollection.horizontalSizeClass == UIUserInterfaceSizeClass.regular) {
            return 36.0
        } else {
            return 30.0
        }
    }
    
    //---------------- VALIDATION ----------------
    //determine whether to block segue or not
	override func shouldPerformSegue(withIdentifier identifier: String, sender: Any?) -> Bool {
		if ((incidentView.text.isEmpty || incidentView.text.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)
			|| (reportSelection == "Other" && (reportTextBox.text == "" || (reportTextBox.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!))
			|| (involveSelection == "Other" && (involveTextBox.text == "" || (involveTextBox.text?.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty)!))
			){
			let alertController = UIAlertController(title: "Invalid Input", message: "Please complete all fields to continue", preferredStyle: .alert)
			
			let OKAction = UIAlertAction(title: "OK", style: .default) { (action) in }
			alertController.addAction(OKAction)
			
			self.present(alertController, animated: true) {}
			
			return false
		}
		if identifier == "locationView"
		{
			if(preloadedData.buildingNames.count == 0 || preloadedData.departmentNames.count == 0)
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
				//setLocationView()
				preload()
				alert.addAction(UIAlertAction(title: "Ok", style: .default, handler: {
					action in
					
					
					self.dismiss(animated: true, completion: nil)
				}))
				self.present(alert, animated: true, completion: nil)
				return false
			}
		}
		
		// by default, transition
		return true

	}
	
}
