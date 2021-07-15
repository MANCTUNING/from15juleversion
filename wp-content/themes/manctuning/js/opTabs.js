class OpTabs {
  constructor(selector) {
    this.selector = selector;
    this.initialize()
  }

  selector
  wrappers
  classes = {
    wrapper: "op-tabs-wrapper",
    contentWrapper: "op-content",
    contentItem: "op-content-item",
    tabsWrapper: "op-tabs",
    tabsItem: "op-tabs-item",
    dataTab: "tabs-tab",
    activeClass: "active",
    initializedContentItemsClass: "op-content-item_initialized"
  }

  initialize() {
    this.wrappers = $(this.selector)
    if (!this.wrappers.length) return
    this.initTabs()
  }

  initTabs() {
    this.wrappers.each((i, wrapper) => {
      const contentItems = $(wrapper).find("." + this.classes.contentItem)
      const tabsContainer = $(wrapper).find("." + this.classes.tabsWrapper)
      const tabItems = tabsContainer.find("." + this.classes.tabsItem)
      $(contentItems).addClass(this.classes.initializedContentItemsClass)
      $(contentItems[0]).addClass(this.classes.activeClass)
      $(tabItems[0]).addClass(this.classes.activeClass)
      if (window.innerWidth > 767) {
        tabItems.css("width", (100 / $(".op-tabs-item").length) + "%")
      }
      tabItems.on("click", ({ currentTarget }) => {
        const tab = currentTarget
        const tabTargetNumber = $(tab).data(this.classes.dataTab)
        const contentItemTarget = $(wrapper).find(`[data-tabs-target="${tabTargetNumber}"]`)
        tabItems.removeClass(this.classes.activeClass)
        $(tab).addClass(this.classes.activeClass)
        if (!contentItemTarget.length) return
        contentItems.removeClass(this.classes.activeClass)
        contentItemTarget.addClass(this.classes.activeClass)
      })
    })
  }
}
