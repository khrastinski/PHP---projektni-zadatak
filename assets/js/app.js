function showSection(sectionId) {
  const sections = document.querySelectorAll("section");
  sections.forEach((section) => (section.style.display = "none"));

  const selectedSection = document.getElementById(sectionId);
  if (selectedSection) selectedSection.style.display = "block";
}

document.addEventListener("DOMContentLoaded", () => {
  const cards = Array.from(document.querySelectorAll(".gallery-card"));

  cards.forEach((card) => {
    const link = card.querySelector("a[href]");
    if (!link) return;

    link.setAttribute("target", "_blank");
    link.setAttribute("rel", "noopener");

    card.addEventListener("keydown", (e) => {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        window.open(link.href, "_blank", "noopener,noreferrer");
      }
    });

    card.addEventListener("click", (e) => {
      const clickedInsideLink = e.target.closest("a[href]");
      if (clickedInsideLink) return;

      window.open(link.href, "_blank", "noopener,noreferrer");
    });
  });
});
