pragma solidity ^0.5.8;

import "./TRC20.sol";

contract HubToken is TRC20 {
  string public name = "HubToken";
  string public symbol = "HUB";
  uint8 public decimals = 18;
  
  uint256 private _initialSupply = 1000000000 * 1e18;
  
  constructor() public{
    _mint(msg.sender, _initialSupply);
  }

}