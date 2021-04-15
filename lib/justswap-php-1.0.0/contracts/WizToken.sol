pragma solidity ^0.5.8;

import "./TRC20.sol";

contract WizToken is TRC20 {
  string public name = "WizToken";
  string public symbol = "WIZ";
  uint8 public decimals = 18;
  
  uint256 private _initialSupply = 1000000000 * 1e18;
  
  constructor() public{
    _mint(msg.sender, _initialSupply);
  }

}