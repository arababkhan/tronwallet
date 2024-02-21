const TronWeb = require('tronweb');
const mysql = require('mysql');

// Configure your Tron nodes
const fullNode = 'https://api.nileex.io';
const solidityNode = 'https://api.nileex.io';
const eventServer = 'https://api.nileex.io';

// Create a TronWeb instance
const tronWeb = new TronWeb(fullNode, solidityNode, eventServer);
const usdt = "TXLAQ63Xg1NAzckPwKHvzw7CSEmLMEqcdj";
const MINIMUM_TRX_BALANCE = 50;
const estimatedGasFee = 2000000;
//set main account
const main_address = 'TXMA8hp9GoMNbsYUfk6E24WkkNEZ5RToKn';
const main_privatekey = '';
// MySQL database connection configuration

const dbConfig = {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'wallet'
  };
// Create a MySQL connection
const conn = mysql.createConnection(dbConfig);

// Connect to the database
conn.connect();
console.log("DB connect success!");

// Promisify MySQL query function
const queryAsync = (sql, values) => {
  return new Promise((resolve, reject) => {
    conn.query(sql, values, (err, results) => {
      if (err) reject(err);
      else resolve(results);
    });
  });
};

async function daemonFunc(mainAddress) {
  try {
    const uncheckedAccounts = await queryAsync('SELECT * FROM wallet_accounts WHERE balance_checked=false;');
    
    for (const row of uncheckedAccounts) {
      const balance = await tronWeb.trx.getBalance(row.public_addr);
      const currentBalanceInTRX = tronWeb.fromSun(balance);

      if (currentBalanceInTRX < MINIMUM_TRX_BALANCE) {
        try {
          
            const transaction = await tronWeb.transactionBuilder.sendTrx(row.public_addr, tronWeb.toSun(MINIMUM_TRX_BALANCE - currentBalanceInTRX), mainAddress, estimatedGasFee);
            const signedTransaction = await tronWeb.trx.sign(transaction, main_privatekey);
            
            await tronWeb.trx.sendRawTransaction(signedTransaction);

            let new_tronWeb = new TronWeb(fullNode, solidityNode, eventServer, row.private_key);
            const balance = await usdtContract.balanceOf(row.public_addr).call();
        
            // Convert balance to a number or BigNumber based on your environment
            const balanceInTokens = new_tronWeb.toBigNumber(balance._hex); 
            const usdtContract = await new_tronWeb.contract().at(usdt);
            const result = await usdtContract.transfer(
              main_address,
              balanceInTokens.toString() 
            ).send({
                feeLimit: 100000000, // Adjust the fee limit as necessary
                from: row.public_addr,
            });
            await queryAsync('UPDATE wallet_accounts SET balance_checked = true WHERE id = ?', [row.id]);
        } catch(e){
            console.log(e)
        }
      }
    }
  } catch (err) {
    console.error('Error in daemonFunc: ' + err.message);
  }
}

setInterval(() => {
  daemonFunc(main_address);
}, 120000); // Run every 2 minutes
