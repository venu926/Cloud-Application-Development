package gym.booking.system;

public class Bookslot {

	private int availableSeats = 3;

	public synchronized void bookSlot(String pname, int numberOfSeats) {
		if ((availableSeats >= numberOfSeats) && (numberOfSeats > 0)) {
			System.out.println("Hi," + pname + " : " + numberOfSeats+ " Seats booked Successfully..");
			availableSeats = availableSeats- numberOfSeats;
		} else
			System.out.println("Hi," + pname + " : Seats Not Available");
	}
}
