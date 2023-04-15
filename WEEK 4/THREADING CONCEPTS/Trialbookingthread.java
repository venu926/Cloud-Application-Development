package gym.booking.system;

public class SlotBookingThread extends Thread {

	private Bookslot Bookslot;
	private String passengerName;
	private int noOfSeatsToBook;

	public SlotBookingThread(Bookslot Bookslot,String passengerName, int noOfSeatsToBook) {
		this.Bookslot = Bookslot;
		this.passengerName = passengerName;
		this.noOfSeatsToBook = noOfSeatsToBook;
	}
	
	public void run() {
		Bookslot.bookSlot(passengerName, noOfSeatsToBook);
	}
}
